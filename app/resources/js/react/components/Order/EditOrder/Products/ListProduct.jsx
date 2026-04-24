import React, { useCallback, useEffect } from "react";
import Currencies from "../../../Currencies";
import useTable from "../../../../libraries/handleTable";
import { useForm } from "../../../../libraries/handleInput";
import InventoryService from '../../../../services/InventoryService'
import { useI18n } from "@/i18n/useI18n";
import CommonDataTableV2 from "@/react/components/CommonDataTableV2";
import StockMovementInService from "@/react/services/StockMovementInService";
import ContentOnTable from "@/react/components/ContentOnTable";
export default function ListProduct({
    add = (product) => { },
    detail = null
}) {
    const { t } = useI18n();
    const columns = [
        { label: "ID", key: "id" },
        {
            label: "PU", key: "purchase_id", render: (value) => {
                return "PU" + value
            }
        },
        {
            label: t("Name"), key: "name", render: (value) => {
                return <ContentOnTable value={value} />
            }
        },
        { label: t("Quantity"), key: "quantity" },
        { label: t("Category"), key: "category",render: (value) => {
                    return <ContentOnTable value={value}/>
                }  },
        {
            label: t("Price"), key: "price", render: (value) => {
                return <Currencies amount={value} />;
            }
        },
        {
            label: t("Warehouse"), key: "warehouse", render: (value) => {
                return <ContentOnTable value={value} />
            }
        },
        {
            label: t("Purchase date"), key: "purchase_date", render: (value) => {
                return value;
            }
        }
    ];
    const table = useTable();
    const search = useForm();
    const getInventories = useCallback((page = 0) => {
        table.setLoading(true);
        StockMovementInService.list({
            ...detail,
            page: page,
            ...search.formData
        })
            .then((resp) => {
                console.log(resp)
                table.setData(resp.message.data);
                table.setLinks(resp.message.links)
                table.setLoading(false);
            })
            .catch((error) => {

            })
    }, [search.formData, detail]);
    useEffect(() => {
        getInventories();
    }, [detail?.customer_group_id])
    return <div className="mt-3">
        <h4 className="h5">{t("Inventory")}</h4>
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
            loading={table.loading}
            columns={columns}
            data={table?.data}
            links={table?.links}
            iconEdit={<i className="bi bi-plus-square"></i>}
            onEdit={(row) => {
                add(row)
            }}
            type={'orderitem'}
            search={search}
            callback={getInventories}
        />
    </div>
}