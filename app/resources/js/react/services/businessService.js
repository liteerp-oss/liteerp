import api from "../common/api";

 const businessService = {
  add: (data) => api.post("/business",data),
  update: (data) => api.put("/business/" + data.id,data),
  list: () => api.get("/business"),
  show: (id) => api.get("/business/" + id),
  view: () => api.get("/view/business")
};
export default businessService;