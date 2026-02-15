import React from "react";
import CommonDataTable from "../../../CommonDataTable";
import Currencies from '../../../Currencies';
import { useI18n } from "@/i18n/useI18n";

export default function ProductAdded({ 
    table = null, 
    form=null, 
    movePage = (page) => {}, 
    loading = false, 
    disabled = false,
    setShowForm = (status) => {}, 
    onDelete = (value) => {}
    }) {
    const {t} = useI18n();
    const columns = [
        { label: t("Name"), key: "name" },
        { label: t("Unit"), key: "unit" },
        {
            label: t('Price'),
            key: "price",
            render: (value) => {
                return <Currencies amount={value}/>
            }
        },

        {
            label: t("Buy"),
            key: "buy_quantity",
            render: (v) => Number(v)
        },

        {
            label: t("Gift"),
            key: "gift_quantity",
            render: (v) => Number(v)
        },

        {
            label: t("Compensation"),
            key: "compensation_quantity",
            render: (v) => Number(v)
        },

        {
            label: t("Conversion"),
            key: "conversion_quantity",
            render: (v) => Number(v)
        },

        {
            label: t("Discount"),
            key: "discount",
            render: (value) => {
                return <span>{value}%</span>
            }
        },

        { label: t("Tax") + " (%)", key: "tax" },
        { label: t("Warehouse"), key: "warehouse" }
    ];

    return (
        <div>
            <h4 className="h5">{t("Added to order")}</h4>
            <CommonDataTable
                columns={columns}
                data={table?.data}
                links={table?.links}
                movePage={movePage}
                onEdit={ disabled ? null : (row) => {
                    form.setFormData(row);
                    form.setIsEdit(true);
                    setShowForm(true)
                }}
                onDelete={disabled ? null :(row) => {
                    onDelete(row)
                }}
                loading={loading}
            />
        </div>
    );
}
