import api from "../common/api";

 const PermissionGroupUserService = {
  add: (data) => api.post("/business-access/permission-group-users", data),
  delete: (data) => api.delete("/business-access/permission-group-users/" + data.id,{
    params: data
  }),
};
export default PermissionGroupUserService;