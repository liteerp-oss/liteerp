import api from "../common/api";

 const InventoryAdjustmentService = {
  list: (data) => api.get(`/business-access/inventory-adjustments`,{
    params: data
  }),
  add: (data) => api.post("/business-access/inventory-adjustments",data),
  view: () => api.get("/business-access/view/inventory-adjustments")
};
export default InventoryAdjustmentService;