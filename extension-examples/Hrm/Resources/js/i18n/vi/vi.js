export default {
    hrm: {
        title: "Quản lý nhân sự",
        desc: "Tiện ích HRM gọn nhẹ để quản lý nhân viên, chấm công và đơn xin nghỉ phép. Thiết kế module, không can thiệp core.",
        leave: {
            add: {
                success: "Tạo mới thành công",
                title: "Thêm đơn nghỉ phép"
            },
            update: {
                success: "Cập nhật thành công"
            },
            start_date: {
                label: "Ngày bắt đầu"
            },
            end_date: {
                label: "Ngày kết thúc"
            },
            reason: "Lý do",
            status: {
                label: "Trạng thái",
                approved: "Đã duyệt",
                pending: "Chờ duyệt",
                rejected: "Từ chối"
            },
            name: "Tên",
            action: "Thao tác",
            form: {
                rejected: "Từ chối",
                pending: "Chờ duyệt",
                approved: "Đã duyệt",
                annual: "Nghỉ phép năm",
                sick: "Nghỉ ốm",
                unpaid: "Nghỉ không lương"
            },
            leave_type: {
                label: "Loại nghỉ phép"
            },
            title: "Đơn xin nghỉ phép"
        },
        export: {
            desc: "Báo cáo tổng hợp tháng hiện tại sẽ được tự động tạo vào ngày đầu tiên của tháng tiếp theo",
            name: "Tên",
            created_at: "Ngày tạo",
            title: "Xuất dữ liệu"
        },
        report: {
            title: "Báo cáo",
            date: "Ngày",
            summary: "Tổng hợp",
            tasks: "Công việc",
            issues: "Vấn đề",
            name: "Tên",
            form: {
                daily_summary: {
                    label: "Tổng kết trong ngày",
                    placeholder: "Tóm tắt công việc của bạn hôm nay"
                },
                tasks_completed: {
                    label: "Công việc đã hoàn thành",
                    placeholder: "Liệt kê các công việc đã hoàn thành hôm nay"
                },
                issue: {
                    label: "Vấn đề",
                    placeholder: "Vấn đề cần báo cáo với quản lý"
                }
            },
            add: {
                title: "Thêm báo cáo"
            },
            detail: {
                title: "Chi tiết báo cáo"
            },
            filter: {
                button: {
                    label: "Tìm kiếm"
                },
                keywords: {
                    label: "Từ khóa",
                    placeholder: "Tìm theo email, tên hoặc ngày"
                }
            }
        },
        attendance: {
            title: "Chấm công",
            date: "Ngày",
            checkin: "Giờ vào",
            checkout: "Giờ ra",
            note: "Ghi chú",
            name: "Tên",
            approved: "Đã duyệt",
            add: {
                title: "Thêm chấm công",
                placeholder: {
                    note: "Ghi chú cho bộ phận nhân sự"
                },
                note: "Ghi chú"
            },
            approve: {
                title: "Duyệt chấm công",
                placeholder: {
                    note: "Ghi chú cho bộ phận nhân sự"
                },
                note: "Ghi chú",
                status: "Trạng thái",
                approved: "Duyệt",
                unapprove: "Không duyệt",
                confirm_button: "Xác nhận"
            },
            update: {
                title: "Cập nhật chấm công",
                note: "Ghi chú",
                placeholder: {
                    note: "Thông tin bổ sung cho HR"
                },
                button: "Xác nhận"
            }
        }
    },
    "permission_label_hrm": "Nhân sự",
    "hrm-nav": "Quản lý nhân sự",
    "hrm-approve-attendance": "Duyệt chấm công",
    "hrm-index-attendance": "Danh sách chấm công",
    "hrm-update-attendance": "Cập nhật chấm công",
    "hrm-create-attendance": "Tạo chấm công",
    "hrm-create-report": "Tạo báo cáo",
    "hrm-index-report": "Danh sách báo cáo",
    "hrm-show-report": "Chi tiết báo cáo",
    "hrm-create-monthly-summary": "Tạo tổng kết tháng",
    "hrm-index-monthly-summary": "Danh sách tổng kết tháng",
    "hrm-show-monthly-summary": "Chi tiết tổng kết tháng",
    "hrm-index-leave": "Danh sách đơn nghỉ phép",
    "hrm-create-leave": "Tạo đơn nghỉ phép",
    "hrm-approve-leave": "Duyệt đơn nghỉ phép"
};
