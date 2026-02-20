import api from "../common/api";

 const PermissionService = {
  view: () => api.get("/business-access/permissions"),
  show: (data) => api.get("/business-access/permissions/" + data.id),
  store: (data) => api.post("/business-access/permissions", data)
};
export default PermissionService;