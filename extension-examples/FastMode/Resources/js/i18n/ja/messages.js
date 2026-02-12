export default {
    fastmode: {
        title: "高速モード",
        subtitle: "高速モード",
        submit: {
            label: "変更を保存"
        },
        form: {
            status: {
                label: "デフォルトの請求書ステータス",
                paid: "支払い済み",
                pending: "未払い",
                partial_payment: "一部支払い"
            }
        },
        desc: `デフォルトの請求書ステータスをどれに設定しますか？ 
               高速モードでは、請求書モジュールの承認ステップをスキップできます。 
               会計担当がいない小規模事業者や、手動または別のシステムで処理している場合に便利です。`,
        success: {
            message: "変更が保存されました"
        }
    }
}
