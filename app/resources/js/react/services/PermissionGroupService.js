import api from "../common/api";

 const PermissionGroupService = {
  list: (data) => api.get("/business-access/permission-groups",{
    params: data
  }),
  update: (data) => api.put("/business-access/permission-groups/" + data.id, data),
  store: (data) => api.post("/business-access/permission-groups", data),
  delete: (data) => api.delete("/business-access/permission-groups/" + data.id,{
    params: data
  }),
};
export default PermissionGroupService;