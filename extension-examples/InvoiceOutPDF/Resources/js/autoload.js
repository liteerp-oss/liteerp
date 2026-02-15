import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import './app'
export default class ServiceProvider extends Extension {
    register() {
        
    }

    boot() {
        console.log('InvoiceOutPDF loaded')
    }
}