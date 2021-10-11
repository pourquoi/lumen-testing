export default {
    save(k, v) {
        try {
            window.localStorage.setItem(k, JSON.stringify(v))
        } catch( err ) {
            // probably private mode
        }
    },
    load(k) {
        return JSON.parse(window.localStorage.getItem(k))
    }
}