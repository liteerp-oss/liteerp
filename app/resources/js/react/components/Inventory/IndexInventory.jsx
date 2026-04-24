import React, { useCallback, useEffect } from 'react'
import useTable from '../../libraries/handleTable'
import InventoryService from '../../services/InventoryService'
import { useForm } from '../../libraries/handleInput'
import { useI18n } from '../../../i18n/useI18n'
import CommonDataTableV2 from '../CommonDataTableV2'

export default function IndexInventory() {
    const { t, lang } = useI18n()
    const table = useTable()
    const search = useForm()
    const getInventory = useCallback(
        (page = 0) => {
            table.setLoading(true)
            InventoryService.list({
                ...search.formData,
                page,
            }).then((resp) => {
                table.setLoading(false)
                table.setData(resp.message.data)
                table.setLinks(resp.message.links)
            })
        },
        [search.formData]
    )
    const getView = useCallback(
        () => {
            InventoryService.view().then((resp) => {
                table.addColums(resp.message.index, (item, data) => (
                    <RenderFormTableByList item={item} data={data} />
                ))
                search.setHookRender(resp.message?.search ?? [])
            })
        },
        [table]
    )

    useEffect(() => {
        table.setColums([
            { key: 'name', label: t('Name') },
            { key: 'quantity', label: t('Quantity') },
            { key: 'sku', label: t('SKU') },
            { key: 'unit', label: t('Unit'),render:(value) => {
                return t(value)
            } },
            { key: 'warehouse', label: t('Warehouse') },
            { key: 'category', label: t('Category') },
        ])
        getInventory()
        getView();
    }, [lang])

    return (
        <div>
            <CommonDataTableV2
                loading={table.loading}
                callback={getInventory}
                data={table.data}
                links={table.links}
                search={search}
                columns={table.colums}

                type={'inventory'}
            />
        </div>
    )
}
