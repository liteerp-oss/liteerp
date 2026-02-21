export default {
    hrm: {
        title: "人事管理",
        desc: "従業員、勤怠、休暇申請を管理する軽量なHRM拡張機能です。コアを変更しないモジュール設計。",
        leave: {
            add: {
                success: "作成が完了しました",
                title: "休暇申請を追加"
            },
            update: {
                success: "更新が完了しました"
            },
            start_date: {
                label: "開始日"
            },
            end_date: {
                label: "終了日"
            },
            reason: "理由",
            status: {
                label: "ステータス",
                approved: "承認済み",
                pending: "承認待ち",
                rejected: "却下"
            },
            name: "名前",
            action: "操作",
            form: {
                rejected: "却下",
                pending: "承認待ち",
                approved: "承認済み",
                annual: "年次休暇",
                sick: "病気休暇",
                unpaid: "無給休暇"
            },
            leave_type: {
                label: "休暇の種類"
            },
            title: "休暇申請"
        },
        export: {
            desc: "今月の集計は翌月の初日に自動生成されます",
            name: "名前",
            created_at: "作成日",
            title: "エクスポート"
        },
        report: {
            title: "レポート",
            date: "日付",
            summary: "概要",
            tasks: "作業内容",
            issues: "問題",
            name: "名前",
            form: {
                daily_summary: {
                    label: "日次サマリー",
                    placeholder: "本日の業務内容をまとめてください"
                },
                tasks_completed: {
                    label: "完了したタスク",
                    placeholder: "本日完了したタスクを記入してください"
                },
                issue: {
                    label: "問題",
                    placeholder: "管理者に報告したい問題"
                }
            },
            add: {
                title: "レポートを追加"
            },
            detail: {
                title: "レポート詳細"
            },
            filter: {
                button: {
                    label: "検索"
                },
                keywords: {
                    label: "キーワード",
                    placeholder: "メール、名前、日付で検索"
                }
            }
        },
        attendance: {
            title: "勤怠管理",
            date: "日付",
            checkin: "出勤",
            checkout: "退勤",
            note: "メモ",
            name: "名前",
            approved: "承認済み",
            add: {
                title: "勤怠を追加",
                placeholder: {
                    note: "人事へのメモ"
                },
                note: "メモ"
            },
            approve: {
                title: "勤怠承認",
                placeholder: {
                    note: "人事へのメモ"
                },
                note: "メモ",
                status: "ステータス",
                approved: "承認",
                unapprove: "未承認",
                confirm_button: "確認"
            },
            update: {
                title: "勤怠を更新",
                note: "メモ",
                placeholder: {
                    note: "人事向けの補足情報"
                },
                button: "確認"
            }
        },
        permission_label_hrm: "人事管理"
    },
    "hrm-nav": "人事管理",
    "hrm-approve-attendance": "勤怠承認",
    "hrm-index-attendance": "勤怠一覧",
    "hrm-update-attendance": "勤怠更新",
    "hrm-create-attendance": "勤怠登録",
    "hrm-create-report": "レポート作成",
    "hrm-index-report": "レポート一覧",
    "hrm-show-report": "レポート詳細",
    "hrm-create-monthly-summary": "月次集計作成",
    "hrm-index-monthly-summary": "月次集計一覧",
    "hrm-show-monthly-summary": "月次集計詳細",
    "hrm-index-leave": "休暇申請一覧",
    "hrm-create-leave": "休暇申請作成",
    "hrm-approve-leave": "休暇申請承認"
};
