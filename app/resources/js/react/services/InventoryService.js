import api from "../common/api";

const InventoryService = {
    add: (data) => api.post("/business-access/inventory", data),
    show: (id) => api.get("/business-access/inventory/" + id),
    list: (data) => api.get("/business-access/inventory",{
        params: data
    }),
    update: (data) => api.put("/business-access/inventory/" + data.id, data),
    view: () => api.get("/business-access/view/inventory")
};
export default InventoryService;