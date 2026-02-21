import App from './app.jsx'
import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/smtp',
            component: App
        })
    }
    boot() {
        console.log('SMTP loadded');
    }
}