export default {
    hrm: {
        title: "Human Resource Management",
        desc: "Lightweight HRM extension for managing employees, attendance, and leave requests. Modular design with no core modification.",
        leave: {
            add: {
                success: "You has been created",
                title: "Add leave"
            },
            update: {
                success: "You has been updated"
            },
            start_date: {
                label: "Start date"
            },
            end_date: {
                label: "End date"
            },
            reason: "Reason",
            status: {
                label: "Status",
                approved: "Approved",
                pending: "Pending",
                rejected: "Rejected"
            },
            name: "Name",
            action: "Action",
            form: {
                rejected: "Rejected",
                pending: "Pending",
                approved: "Approved",
                annual: "Annual",
                sick: "Sick",
                unpaid: "Unpaid"
            },
            leave_type: {
                label: "Type"
            },
            title: "Leave request"
        },
        export: {
            desc: "Currently month summary will auto create at first date of next month",
            name: "Name",
            created_at: "Created at",
            title: "Export"
        },
        report: {
            title: "Report",
            date: "Date",
            summary: "Summary",
            tasks: "Tasks",
            issues: "Issues",
            name: "Name",
            form: {
                daily_summary: {
                    label: "Dailly summary",
                    placeholder: "Summary your work in today"
                },
                tasks_completed: {
                    label: "Tasks completed",
                    placeholder: "List tasks completed in today"
                },
                issue: {
                    label: "Issue",
                    placeholder: "Your issue request to manager"
                }
            },
            add: {
                title: "Add report"
            },
            detail: {
                title: "Detail report"
            },
            filter: {
                button: {
                    label: "Search"
                },
                keywords: {
                    label: "Keywords",
                    placeholder: "Search email, name or date"
                }
            }
        },
        attendance: {
            title: "Time Attendance",
            date: "Date",
            checkin: "Check in",
            checkout: "Check out",
            note: "Note",
            name: "Name",
            approved: "Approved",
            add: {
                title: "Add attendance",
                placeholder: {
                    note: "Note anything for HR"
                },
                note: "Note"
            },
            approve: {
                title: "Approve attendance",
                placeholder: {
                    note: "Note anything for HR"
                },
                note: "Note",
                status: "Status",
                approved: "Approved",
                unapprove: "Unapprove",
                confirm_button: "Confirm"
            },
            update: {
                title: "Update attendance",
                note: "Note",
                placeholder: {
                    note: "Anything for HR"
                },
                button: "Confirm" 
            }
        }
    },
    "hrm-nav": "Human Resource Management",
    "hrm-approve-attendance": "Approve Attendance",
    "hrm-index-attendance": "Attendance List",
    "hrm-update-attendance": "Update Attendance",
    "hrm-create-attendance": "Create Attendance",
    "hrm-create-report": "Create Report",
    "hrm-index-report": "Report List",
    "hrm-show-report": "Report Details",
    "hrm-create-monthly-summary": "Create Monthly Summary",
    "hrm-index-monthly-summary": "Monthly Summary List",
    "hrm-show-monthly-summary": "Monthly Summary Details",
    "hrm-index-leave": "Leave Request List",
    "hrm-create-leave": "Create Leave Request",
    "hrm-approve-leave": "Approve Leave Request"
};