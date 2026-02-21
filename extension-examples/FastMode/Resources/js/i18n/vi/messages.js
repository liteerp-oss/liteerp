export default {
    fastmode: {
        title: "Chế độ nhanh",
        subtitle: `Fastmode là tính năng giúp rút gọn quy trình xử lý đơn hàng bằng cách bỏ qua bước phê duyệt của kế toán. 
                    Thông thường, đơn hàng sẽ được duyệt nghiệp vụ, sau đó chuyển qua kế toán xác nhận trước khi đến kho. 
                    Với Fastmode, hệ thống cho phép chuyển đơn trực tiếp đến kho sau khi được duyệt ban đầu, 
                    giúp tiết kiệm thời gian và tăng tốc độ vận hành. 
                    Tính năng này đặc biệt phù hợp với doanh nghiệp nhỏ có quy trình tinh gọn và cần xử lý nhanh.`,
        submit: {
            label: "Lưu thay đổi"
        },
        form: {
            status: {
                label: "Trạng thái hóa đơn mặc định",
                paid: "Đã thanh toán",
                pending: "Chờ thanh toán",
                partial_payment: "Thanh toán một phần"
            }
        },
        desc: `Điều này có nghĩa là bạn muốn trạng thái hóa đơn mặc định là gì? 
               Chế độ nhanh cho phép bạn bỏ qua bước phê duyệt trong module Hóa đơn. 
               Tính năng này hữu ích cho các doanh nghiệp nhỏ không có kế toán 
               hoặc đang xử lý thủ công hay tại hệ thống khác.`,
        success: {
            message: "Bạn đã lưu thay đổi thành công"
        }
    },
    "fastmode-index": "Xem cài đặt",
    "fastmode-create": "Lưu cài đặt",
    "fastmode-test": "Gửi thử"
}
