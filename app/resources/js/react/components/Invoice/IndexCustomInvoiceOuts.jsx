import React, { useCallback, useEffect, useRef, useState } from 'react'
import useTable from '../../libraries/handleTable';
import { isoToDateTime } from '../../libraries/common';
import { PopupLayout } from '../../layouts/PopupLayout'
import { InputForm } from '../UI/Input/InputForm'
import { useForm } from '../../libraries/handleInput';
import { Select } from '../UI/Input/Select';
import { usePopup } from '../popups/PopupContext'
import Currencies from '../Currencies';
import StatusBadge from '../StatusBadge';
import CustomInvoiceOutService from '../../services/CustomInvoiceOutService';
import TextArea from '../UI/Input/Textarea'
import SearchSelect from '../UI/Input/SearchSelect'
import CustomerService from '../../services/CustomerService';
import ContentOnTable from '../ContentOnTable';
import RenderFieldTableByList from '../RenderFieldTableByList';
import RenderFormFieldByList from '../RenderFormFieldByList';
import { useI18n } from '../../../i18n/useI18n';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function IndexCustomInvoiceOuts() {
    const { t, lang } = useI18n();
    const [customers, setCustomers] = useState([]);
    const search = useForm();
    const form = useForm();
    const table = useTable();
    const { openPopup } = usePopup();
    const [showForm, setShowForm] = useState(false);

    const getInvoices = useCallback((page = 0) => {
        table.setLoading(true);
        CustomInvoiceOutService.list({
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

    const update = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null);
        CustomInvoiceOutService.update(form.formData)
            .then((resp) => {
                getInvoices();
                openPopup({
                    type: 'success',
                    message: t('You has been updated')
                })
                setShowForm(false)
                form.setLoading(false)
                resetForm();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false)
            })
    }, [form.formData]);

    const add = useCallback(() => {
        form.setLoading(true)
        form.setFormErrors(null);
        CustomInvoiceOutService.add(form.formData)
            .then((resp) => {
                getInvoices();
                openPopup({
                    type: 'success',
                    message: t('You has been added')
                })
                setShowForm(false)
                form.setLoading(false)
                resetForm();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
                form.setLoading(false)
            })
    }, [form.formData]);

    const onEdit = (row) => {
        form.setFormData(row)
        form.setIsEdit(true);
        setShowForm(true)
    }

    const resetForm = () => {
        form.setFormData(null)
        form.setIsEdit(null);
    }

    const destroy = useCallback((row) => {
        CustomInvoiceOutService.delete(row)
            .then((resp) => {
                getInvoices();
                openPopup({
                    type: 'success',
                    message: t('You has been deleted')
                })
                resetForm();
            })
            .catch((error) => {
                if (error.response.data?.errors) {
                    form.setFormErrors(error.response.data?.errors);
                }
                if (error.response.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data?.message
                    })
                }
            })
    }, []);

    const onDelete = (row) => {
        openPopup({
            type: 'warning',
            message: t('Do you wanna to delete?'),
            onConfirm: () => {
                destroy(row)
            }
        })
    }

    const getCustomers = useCallback((keywords = '', callback = null) => {
        CustomerService.list({
            keywords: keywords
        })
            .then((resp) => {
                if (callback) {
                    callback();
                }
                setCustomers(resp.message.data)
            })
            .catch((error) => { })
    }, [])

    const view = useCallback((row) => {
        CustomInvoiceOutService.view(row)
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => {
                    return <RenderFieldTableByList item={item} data={data} />
                })
                form.setHookRender(resp.message.form)
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

    useEffect(() => {
        table.setColums([
            {
                label: t("ID"),
                key: "id"
            },
            {
                label: t("Customer"),
                key: "customer_name"
            },
            {
                label: t("Description"),
                key: "description",
                render: (value) => {
                    return <ContentOnTable value={value} />
                }
            },
            {
                label: t("Invoice no"),
                key: "document_no"
            },
            {
                label: t("Amount"),
                key: "amount",
                render: (value) => <span><Currencies amount={value} /></span>,
            },
            {
                label: t("Status"),
                key: "approved",
                render: (value) => {
                    return <StatusBadge status={value ? 'approved' : 'unapproved'} />
                }
            },
            {
                label: t("Invoice date"),
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
            }
        ])
        getInvoices();
        view();
    }, [lang])

    return <div>
        <CommonDataTableV2
            add={() => {
                setShowForm(true)
            }}
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
            loading={table.loading}
            columns={table.colums}
            data={table.data}
            links={table.links}
            onShow={onEdit}
            callback={getInvoices}
            onDelete={onDelete}
            type={'custominvoiceout'}
        />
        {showForm ? <PopupLayout
            loading={form.loading}
            onClose={() => {
                setShowForm(false)
                resetForm();
            }}
            onConfirm={form.isEdit ? update : add}
            confirmText={form.isEdit ? t('Save change') : t('Add new')}
            title={t('Custom invoice out')}>
            <div>
                <div className='row'>
                    <div className='form-group col-6'>
                        <label>{t("Invoice no")}</label>
                        <InputForm type='text'
                            value={form.formData?.document_no}
                            errorMessage={form.formErrors?.document_no}
                            name='document_no'
                            handleChange={form.handleChange}
                        />
                    </div>
                    <div className='form-group col-6'>
                        <label>{t("Customer")}</label>
                        <SearchSelect
                            search={getCustomers}
                            value={form.formData?.customer_id}
                            errorMessage={form.formErrors?.customer_id}
                            name='customer_id'
                            changeValue={form.handleChangeByKey}
                            options={customers.map((item) => {
                                return {
                                    value: item.id,
                                    label: item.name
                                }
                            })}
                            defaultKeywords={form.formData?.customer_name}
                        />
                    </div>
                </div>
                <div className='form-group mt-3'>
                    <label>{t("Invoice date")}</label>
                    <InputForm type='date'
                        value={isoToDateTime(form.formData?.invoice_date)}
                        errorMessage={form.formErrors?.invoice_date}
                        name='invoice_date'
                        handleChange={form.handleChange}
                    />
                </div>
                <div className='form-group mt-3'>
                    <label>{t("Amount")}</label>
                    <InputForm type='number'
                        value={form.formData?.amount}
                        errorMessage={form.formErrors?.amount}
                        name='amount'
                        handleChange={form.handleChange}
                    />
                </div>
                <div className='form-group mt-3'>
                    <label>{t("Description")}</label>
                    <TextArea
                        value={form.formData?.description}
                        errorMessage={form.formErrors?.description}
                        name='description'
                        handleChange={form.handleChange}
                    />
                </div>
                <div className='form-group mt-3'>
                    <label>{t("Payment status")}</label>
                    <Select
                        value={form.formData?.payment_status}
                        errorMessage={form.formErrors?.payment_status}
                        name='payment_status'
                        handleChange={form.handleChange}
                        options={[
                            { value: 'paid', label: t('Paid') },
                            { value: 'partial_payment', label: t('Partial payment') },
                            { value: 'pending', label: t('Pending') }
                        ]}
                    />
                </div>
                <div className='form-group mt-3'>
                    <label>{t("Status")}</label>
                    <InputForm
                        width={20}
                        value={form.formData?.approved}
                        errorMessage={form.formErrors?.approved}
                        name='approved'
                        type='checkbox'
                        handleChange={form.handleChange}
                    />
                    <span>{t("This mean status invoice, if uncheck then system to understand is not working")}</span>
                </div>
                {form.hookRender.map((item, index) => {
                    return <div className='form-group mt-3' key={index}>
                        <RenderFormFieldByList item={item} form={form} />
                    </div>
                })}
            </div>
        </PopupLayout> : null}
    </div>
}