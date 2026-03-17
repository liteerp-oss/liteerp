import React from "react";
import Currencies from '../../../Currencies';
import { useI18n } from "@/i18n/useI18n";
import CommonDataTableV2 from "@/react/components/CommonDataTableV2";
import ContentOnTable from "@/react/components/ContentOnTable";

export default function ProductAdded({
    table = null,
    form = null,
    loading = false,
    disabled = false,
    setShowForm = (status) => { },
    onDelete = (value) => { },
    search = null,
    callback = () => { }
}) {
    const { t } = useI18n();
    const columns = [
        { label: t("Name"), key: "name",render: (value) => {
            return <ContentOnTable value={value}/>
        } },
        { label: t("PU"), key: "purchase_id",render:(value) => {
            return "PU"+value
        } },
        {
            label: t('Price'),
            key: "price",
            render: (value) => {
                return <Currencies amount={value} />
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
        { label: t("Warehouse"), key: "warehouse",render: (value) => {
            return <ContentOnTable value={value}/>
        }  }
    ];

    return (
        <div>
            <h4 className="h5">{t("Added to order")}</h4>
            <CommonDataTableV2
                config={{
                    default: [{
                        key: "order_by",
                        placeholder: t("Order by"),
                        options: [
                            { value: 'ASC', label: t('Oldest') },
                            { value: 'DESC', label: t('Newest') },
                        ],
                        type: "select",
                        label: t("Order by"),
                        col: "col-6"
                    }, {
                        key: "keywords",
                        placeholder: t("Keywords"),
                        type: "text",
                        label: t("Search"),
                        col: "col-6"
                    }]
                }}
                search={search}
                columns={columns}
                data={table?.data}
                links={table?.links}
                onEdit={disabled ? null : (row) => {
                    form.setFormData(row);
                    form.setIsEdit(true);
                    setShowForm(true)
                }}
                onDelete={disabled ? null : (row) => {
                    onDelete(row)
                }}
                loading={table.loading}
                type={'orderitem'}
                callback={callback}
            />
        </div>
    );
}
