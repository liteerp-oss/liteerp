import api from "../common/api";

const WarehouseService = {
  add: (data) => api.post("/business-access/warehouse", data),
  list: (data) => api.get("/business-access/warehouse", {
    params: data
  }),
  update: (data) => api.put("/business-access/warehouse/" + data.id, data),
  delete: (data) => api.delete("/business-access/warehouse/" + data.id, {
    params: data
  }),
  view: () => api.get("/business-access/view/warehouse")
};
export default WarehouseService; 