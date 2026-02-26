import React from 'react'
import { useCallback, useEffect, useState } from "react";
import { useForm } from "../../libraries/handleInput";
import InvoiceInService from "../../services/InvoiceInService";
import useTable from "../../libraries/handleTable";
import { usePopup } from "../popups/PopupContext";
import { useNavigate } from 'react-router-dom';
import Currencies from '../Currencies'
import StatusBadge from '../StatusBadge'
import RenderFieldTableByList from '../RenderFieldTableByList'
import { useI18n } from '../../../i18n/useI18n';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function IndexInvoiceIns() {
    const { t,lang } = useI18n();
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const table = useTable();
    const search = useForm();

    const handleEdit = useCallback((row) => {
        navigate('/invoice-ins?form=invoicein&id=' + row.id)
    }, []);

    const listInvoice = useCallback((page = 0) => {
        table.setLoading(true);
        InvoiceInService.list({
            page: page,
            ...search.formData
        })
            .then((resp) => {
                table.setData(resp.message.data);
                table.setLinks(resp.message.links);
                table.setLoading(false);
            })
            .catch((error) => {
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            });
    }, [search.formData]);

    const view = useCallback((page = 0) => {
        InvoiceInService.view()
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
            });
    }, []);

    useEffect(() => {
        table.setColums([
            {
                label: t("Supplier"),
                key: "unit_name",
                render: (value) => value ?? <span className="text-muted fst-italic">{value}</span>,
            },
            {
                label: t("Purchase ID"),
                key: "purchase_id",
                render: (value) => {
                    return <span className="">PU{value}</span>
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
                render: (value) => <span>
                    <Currencies amount={value} />
                </span>,
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
                },
            },
            {
                label: t("Import date"),
                key: "invoice_date",
                render: (value) =>
                    value ?? "",
            },
            {
                label: t("Payment"),
                key: "payment_status",
                render: (value) => {
                    return <StatusBadge status={value} />
                }
            },
            {
                label: t("Purchase status"),
                key: "purchase_status",
                render: (value) => {
                    return <StatusBadge status={value} />
                }
            },
            {
                label: t("Amount paid"),
                key: "amount_paid",
                render: (value) => {
                    return <Currencies amount={value}/>
                }
            },
        ])
        listInvoice();
        view();
        
    }, [lang]);

    return <div>
        <CommonDataTableV2
            loading={table.loading}
            
                search={search}
            columns={table.colums}
            data={table.data}
            links={table.links}
            onShow={ handleEdit}
            callback={listInvoice}
            type={'invoicein'}
        />
    </div>
}