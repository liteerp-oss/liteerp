import Extension from '@core/Extension'
import './app'
export default class ServiceProvider extends Extension {
    register() {
        
    }

    boot() {
        console.log('Invoice In PDF loaded')
    }
}