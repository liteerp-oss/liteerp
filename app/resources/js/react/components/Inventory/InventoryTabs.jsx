import React, { useCallback, useEffect } from 'react'
import useTable from '../../libraries/handleTable'
import InventoryService from '../../services/InventoryService'
import CommonDataTable from '../CommonDataTable'
import SearchInput from '../UI/Input/SearchInput'
import { useForm } from '../../libraries/handleInput'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function InventoryTabs() {
    const { t } = useI18n()
    const table = useTable()
    const search = useForm()
    const getInventory = useCallback(
        (page = 0) => {
            table.setLoading(true)
            InventoryService.list({
                keywords: search.formData?.keywords ?? '',
                page,
            }).then((resp) => {
                table.setLoading(false)
                table.setData(resp.message.data)
                table.setLinks(resp.message.links)
            })
        },
        [search.formData?.keywords]
    )

    useEffect(() => {
        getInventory()
    }, [])

    return (
        <div>
            <CommonDataTableV2
                loading={table.loading}
                callback={getInventory}
                data={table.data}
                links={table.links}
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
                    },{
                        key: "keywords",
                        placeholder: t("Keywords"),
                        type: "text",
                        label: t("Search"),
                        col: "col-6"
                    }]
                }}
                search={search}
                columns={[
                    { key: 'id', label: t('ID') },
                    { key: 'name', label: t('Name') },
                    { key: 'quantity', label: t('Quantity') },
                    { key: 'reserved_qty', label: t('Reserved quantity') },
                    { key: 'sku', label: t('SKU') },
                    { key: 'unit', label: t('Unit') },
                    { key: 'warehouse', label: t('Warehouse') },
                    { key: 'category', label: t('Category') },
                ]}
            />
        </div>
    )
}
