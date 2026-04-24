import React, { useCallback, useEffect, useMemo, useState } from 'react'
import ProductService from '../../services/ProductService'
import { useForm } from '../../libraries/handleInput'
import useTable from '../../libraries/handleTable'
import { usePopup } from '../popups/PopupContext'
import LoadImage from '../LoadImage'
import { useI18n } from '../../../i18n/useI18n'
import RenderFieldTableByList from '../RenderFieldTableByList'
import CommonDataTableV2 from '../CommonDataTableV2'
import { useNavigate } from 'react-router-dom'
export default function ListProducts() {
    const { t,lang } = useI18n()
    const { openPopup } = usePopup()
    const navigate = useNavigate()

    const form = useForm()
    const search = useForm()
    const table = useTable()

    const getProducts = useCallback(
        (page = 0) => {
            table.setLoading(true)
            ProductService.list({
                page,
                ...search.formData
            })
                .then((resp) => {
                    table.setData(resp.message.data)
                    table.setLinks(resp.message.links)
                    table.setLoading(false)
                })
                .catch((error) => {
                    if (error.response?.data?.message) {
                        openPopup({
                            type: 'error',
                            message: error.response.data.message,
                        })
                    }
                })
        },
        [search]
    )

    const handEdit = (row) => {
        navigate('/products?product=' + row.id)
    }

    const destroy = useCallback((row) => {
        ProductService.delete(row).then(() => {
            openPopup({
                type: 'success',
                message: t('Product has been deleted'),
            })
            getProducts()
        })
    }, [])

    const handleDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Are you sure to delete?'),
            onConfirm: () => destroy(row),
        })
    }
    const view = useCallback(() => {
        table.setLoading(true)
        ProductService.view()
            .then((resp) => {
                form.setHookRender(resp.message.form)
                search.setHookRender(resp.message.form)
                table.addColums(resp.message.index, (item, data) => {
                    return <RenderFieldTableByList item={item} data={data} />
                })
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    })
                }
            })
    }, [])
    useEffect(() => {
        table.setColums([
            { label: t('ID'), key: 'id' },
            {
                label: t('Thumbnail'),
                key: 'image',
                render: (url) => <LoadImage width={35} height={35} url={url} />,
            },
            { label: t('Name'), key: 'name' },
            { label: t('SKU'), key: 'sku' },
            { label: t('Unit'), key: 'unit',render: (value) => {
                return t(value)
            } },
            { label: t('Category'), key: 'category' },
        ])
        getProducts()
        view();
    }, [lang])

    return (
        <div className="mt-3">
            <CommonDataTableV2
                add={() => {
                    navigate('/products?product=add')
                }}
                
                search={search}
                callback={getProducts}
                loading={table.loading}
                columns={table.colums}
                data={table.data}
                links={table.links}
                onEdit={handEdit}
                onDelete={handleDelete}
                type={'product'}
            />
        </div>
    )
}
