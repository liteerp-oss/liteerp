import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import CustomHomePageApp from './app.jsx'
export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/customhomepage',
            component: CustomHomePageApp
        })
    }
    boot() {
        //console.log('CustomHomePage loadded');
    }
}