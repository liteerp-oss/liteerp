import api from '@common/api'
const fastmodeService = {
    all: (data) => api.get('extension/fastmode', {
        params: data
    }),
    add: (data) => api.post('extension/fastmode', data)
}
export default fastmodeService;