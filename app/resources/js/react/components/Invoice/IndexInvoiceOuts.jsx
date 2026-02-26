import React, { useCallback, useEffect, useState } from 'react'
import InvoiceOutService from '../../services/InvoiceOutService';
import useTable from '../../libraries/handleTable';
import { isoToDateTime } from '../../libraries/common';
import { useForm } from '../../libraries/handleInput';
import { usePopup } from '../popups/PopupContext'
import { useNavigate } from 'react-router-dom';
import Currencies from '../Currencies';
import StatusBadge from '../StatusBadge';
import RenderFieldTableByList from '../RenderFieldTableByList'
import { useI18n } from '../../../i18n/useI18n';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function IndexInvoiceOuts() {
    const { t, lang } = useI18n();
    const navigate = useNavigate();
    const search = useForm();
    const table = useTable();
    const { openPopup } = usePopup();
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
        navigate('/invoice-outs?form=invoiceout&id=' + row.id)
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
                label: t("Invoice no"),
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
                label: t("Total price"),
                key: "total",
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
                label: t("Order date"),
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
    }, [lang])

    return <div>
        <CommonDataTableV2
            search={search}
            loading={table.loading}
            columns={table.colums}
            data={table.data}
            links={table.links}
            onShow={onEdit}
            callback={getInvoices}
            type={'invoiceout'}
        />
    </div>
}