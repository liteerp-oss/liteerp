import React, { useCallback, useEffect } from "react";
import CommonDataTable from "../../../CommonDataTable";
import Currencies from "../../../Currencies";
import useTable from "../../../../libraries/handleTable";
import { useForm } from "../../../../libraries/handleInput";
import InventoryService from '../../../../services/InventoryService'
import { useI18n } from "@/i18n/useI18n";
export default function ListProduct({
    add = (product) => { },
    loading = false,
    detail = null 
}) {
    const {t} = useI18n();
    const columns = [
        { label: "ID", key: "id" },
        { label: t("Name"), key: "name" },
        { label: t("Quantity"), key: "quantity" },
        { label: t("Category"), key: "category" },
        {
            label: t("Price"), key: "price", render: (value) => {
                return <Currencies amount={value}/>;
            }
        },
        {
            label: t("Warehouse"), key: "warehouse", render: (value) => {
                return value;
            }
        }
    ];
    const table = useTable();
    const search = useForm();
    const getInventories = useCallback((page = 0) => {
        table.setLoading(true);
        InventoryService.list({
            keywords: search.formData?.keywords ?? '',
            page: page,
            customer_group_id: detail?.customer_group_id
        })
            .then((resp) => {
                console.log(resp)
                table.setData(resp.message.data);
                table.setLinks(resp.message.links)
                table.setLoading(false);
            })
            .catch((error) => {

            })
    }, [search.formData?.keywords,detail]);
    useEffect(() => {
        getInventories();
    },[detail?.customer_group_id])
    return <div className="mt-3">
        <h4 className="h5">{t("Inventory")}</h4>
        <CommonDataTable
            loading={table.loading}
            columns={columns}
            data={table?.data}
            links={table?.links}
            iconEdit={<i className="bi bi-plus-square"></i>}
            onEdit={(row) => {
                add(row)
            }}
        />
    </div>
}