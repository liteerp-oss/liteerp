import api from '@common/api'
const smtpService = {
    show: (data) => api.get('extensions/smtp',data),
    save: (data) => api.post('extensions/smtp',data),
    send: (data) => api.post('extensions/smtp-test',data)
}
export default smtpService;