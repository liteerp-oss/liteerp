import api from "@/react/common/api";

const PusherService = {
  config: () => api.get("/extensions/pusher-setting/config").then(r => r?.message?.config),
  save: (data) => api.post("/extensions/pusher-setting/save", data).then(r => r?.message?.config),
};
export default PusherService;
