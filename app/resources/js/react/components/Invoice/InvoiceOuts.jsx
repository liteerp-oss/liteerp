import React, { useCallback, useEffect, useState } from 'react'
import InvoiceOutService from '../../services/InvoiceOutService';
import CommonDataTable from '../CommonDataTable';
import useTable from '../../libraries/handleTable';
import { isoToDateTime } from '../../libraries/common';
import { useForm } from '../../libraries/handleInput';
import { Select } from '../UI/Input/Select';
import { usePopup } from '../popups/PopupContext'
import { useNavigate } from 'react-router-dom';
import Currencies from '../Currencies';
import SearchInput from '../UI/Input/SearchInput';
import StatusBadge from '../StatusBadge';
import RenderFieldTableByList from '../RenderFieldTableByList'
import { RenderTableSearch } from '../RenderTableSearch';
import PrimaryButton from '../UI/Buttons/PrimaryButton';
import { useI18n } from '../../../i18n/useI18n';
import { useSelector } from 'react-redux';
import PERMISSIONS from '../../common/permission';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function InvoiceOuts() {
    const { t } = useI18n();
    const navigate = useNavigate();
    const search = useForm();
    const table = useTable();
    const { openPopup } = usePopup();
    const roles = useSelector((state) => state.businessRole.role);
    const getInvoices = useCallback((page = 0) => {
        table.setLoading(true);
        InvoiceOutService.list({
            page: page,
            keywords: search?.formData?.keywords ?? '',
            payment_status: search?.formData?.payment_status ?? '',
            order_by: search.formData?.order_by ?? ''
        })
            .then((resp) => {
                table.setData(resp.message.data)
                table.setLinks(resp.message.links)
                table.setLoading(false);
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, [table, search.formData]);

    const view = useCallback(() => {
        InvoiceOutService.view()
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => {
                    return <RenderFieldTableByList item={item} data={data} />
                })
                search.setHookRender(resp.message.search)
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, []);

    const onEdit = (row) => {
        navigate('/invoices?form=invoiceout&id=' + row.id)
    }

    useEffect(() => {
        table.setColums([
            {
                label: t("Customer"),
                key: "customer_name",
                render: (value) => value ?? <span className="text-muted fst-italic">{value}</span>,
            },
            {
                label: t("Order ID"),
                key: "order_id",
                render: (value) => {
                    return <span className="">OD{value}</span>
                },
            },
            {
                label: t("Invoice no"), // Sử dụng Invoice no thay cho Document no để đồng bộ
                key: "document_no",
                render: (value) => value ?? <span className="text-muted fst-italic">{value}</span>,
            },
            {
                label: t("Subtotal"),
                key: "subtotal",
                render: (value) => <span><Currencies amount={value} /></span>,
            },
            {
                label: t("Tax"),
                key: "tax",
                render: (value) => <span><Currencies amount={value} /></span>,
            },
            {
                label: t("Total price"), // Đồng bộ với key Total price trong i18n
                key: "total_adjusted",
                render: (value) => <strong><Currencies amount={value} /></strong>,
            },
            {
                label: t("Status"),
                key: "approved",
                render: (value) => {
                    return <StatusBadge status={value ? 'approved' : 'unapproved'} />
                }
            },
            {
                label: t("Order date"), // Đồng bộ với key Order date trong i18n
                key: "invoice_date",
                render: (value) =>
                    value ? isoToDateTime(value) : "",
            },
            {
                label: t("Payment"),
                key: "payment_status",
                render: (value) => {
                    return <StatusBadge status={value} />
                }
            },
            {
                label: t("Order status"),
                key: "order_status",
                render: (value) => {
                    return <StatusBadge status={value} />
                }
            },
            {
                label: t("Amount paid"),
                key: "amount_paid",
                render: (value) => {
                    return <Currencies amount={value} />
                }
            },
        ])
        getInvoices();
        view();
    }, [])

    return <div>
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
                    },{
                        key: "keywords",
                        placeholder: t("Keywords"),
                        type: "text",
                        label: t("Search"),
                        col: "col-6"
                    }]
                }}
                search={search}
            loading={table.loading}
            columns={table.colums}
            data={table.data}
            links={table.links}
            onEdit={roles?.includes(PERMISSIONS.INVOICE_OUT.SHOW) ? onEdit : null}
            callback={getInvoices}
        />
    </div>
}