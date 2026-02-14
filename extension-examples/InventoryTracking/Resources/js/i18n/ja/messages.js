export default {
    inventorytracking: {
        title: "在庫",
        desc: "在庫の最小数量を設定します",
        form: {
            min: {
                placeholder: "最小数量",
                label: "最小",
                explain: "これは全商品の在庫最小数量を設定する項目です。商品の数量が設定値と同じになった場合、システムは通知を送信します。"
            },
            button: {
                label: "変更を保存"
            },
            success: {
                message: "変更が正常に保存されました"
            }
        },
    }
}
