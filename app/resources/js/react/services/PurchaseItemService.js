import api from "../common/api";

 const PurchaseItemService = {
  add: (data) => api.post("/business-access/purchase-items",data),
  show: (id) => api.get("/business-access/purchase-items/" + id),
  list: (data) => api.get("/business-access/purchase-items",{
    params: data
  }),
  update: (data) => api.put("/business-access/purchase-items/" + data.id,data),
  delete: (data) => api.delete("/business-access/purchase-items/"  + data.id,{
    params: data
  }),
  view: (data) => api.get("/business-access/view/purchase-items",{
    params: data
  })
};
export default PurchaseItemService;