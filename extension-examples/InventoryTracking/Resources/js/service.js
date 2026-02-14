import api from '@common/api'
const inventorytrackingService = {
    all: (data) => api.get('extension/inventory', {
        params: data
    }),
    add: (data) => api.post('extension/inventory', data)
}
export default inventorytrackingService;