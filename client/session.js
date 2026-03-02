/* eslint-disable array-callback-return */
/* eslint-disable no-underscore-dangle */
/** клиентская часть плагина роутера, позволяет
 * аторизовать с помощью логин/пароль или идентификатора сессии sid
 *
 * Ex:
 * import router from 'fmihel-php-router-client';
 * import Session from 'fmihel-php-session-client';
 * let session = new Session(router);
 *
 * session.on('autprize',()=>{
 *     console.log('after autorize');
 * })
 * session.on('logout',()=>{
 *     console.log('after logout');
 * })
 *
 * session.autorize({pass:'xxx',login:'name'});
 *
 * session.logout();
 *
 */

export default class Session {
    constructor(router) {
        this.private = {
            enabled: false,
            data: {}, // информация о сессии
        };
        this.router = router;

        this.before = this.before.bind(this);
        this.after = this.after.bind(this);
        this.events = { autorize: [], logout: [] };
        this.paths = ['session/autorize', 'session/logout'];
        this.ZERRO_SESSION = '0000-0000-0000';
    }

    /** обработчик перед отправкой,
     * в отправляемый пакет добавляет информацию о сесси
    */
    before(pack) {
        return { ...pack, session: this.private.data };
    }

    /** обработчик сразу, как приходит информация
     * в отправляемый пакет добавляет информацию о сесси
    */
    after(pack) {
        const { to, session } = pack;
        // если это не информация об авторизации и пакет pack.session === [] то сервер не подтвердил авторизацию
        // и значит не производил обработку входящег пакета, и значит разрываем авторизацию
        if (this.paths.indexOf(to) < 0 && (Array.isArray(session) && session.length === 0)) {
            this._close();
        }

        if (this.paths.indexOf(to) < 0 && session.sid !== this.ZERRO_SESSION) {
            this.private.data = { ...session };
        }

        return pack;
    }

    _close() {
        if (this.private.enabled) {
            this.private.enabled = false;
            this.private.data = {};
            this.do('logout');
            return true;
        }
        return false;
    }

    /** признак, авторизованы или нет */
    enabled() {
        return this.private.enabled;
    }

    /** запрос на авторизацию, будет обработан плагином php-session
     * можно отправить или login и pass или sid
    */
    autorize({ login, pass, sid }) {
        const t = this;
        return t.router.send({
            to: 'session/autorize',
            data: {
                ...(login ? { login } : {}),
                ...(pass ? { pass } : {}),
                ...(sid ? { sid } : {}),
            },
        }).then((data) => {
            if ('sid' in data) {
                const prev = { ...t.private, data: { ...t.private.data } };
                t.private.enabled = true;
                t.private.data = { ...data };
                if (!prev.enabled || prev.data.sid !== data.sid) t.do('autorize');
            } else {
                t._close();
                throw new Error('data is empty');
            }
            return data;
        }).catch((e) => {
            t._close();
            throw new Error(e);
        });
    }

    /** разрыв авторизации */
    logout() {
        const { sid } = this.private.data;
        if (this._close()) {
            this.router.send({ to: 'session/logout', data: { sid } });
        }
    }

    /** добавляет события autorize logout , возвращает метод отмены события */
    on(event, callback) {
        const t = this;
        if (!(event in t.events)) throw Error(` event="${event}" not in ${this.events.join(',')}`);
        t.events[event].push(callback);
        return () => { t.events[event] = t.events[event].filter((cb) => cb !== callback); };
    }

    /** выполняет все сохраненные ф-ции соотвествующие событию */
    do(event, param = {}) {
        const t = this;
        t.events[event].map((callback) => {
            callback({ param, sender: t, event });
        });
    }
}
