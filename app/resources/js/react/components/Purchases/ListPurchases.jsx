import React, { useCallback, useEffect } from 'react';
import PurchaseService from '../../services/PurchaseService';
import { useNavigate } from 'react-router-dom';
import { usePopup } from '../popups/PopupContext';
import useTable from '../../libraries/handleTable';
import { useForm } from '../../libraries/handleInput';
import PageHead from '../PageHead';
import Currencies from '../Currencies';
import { isoToDateTime } from '../../libraries/common';
import StatusBadge from '../StatusBadge';
import PaymentMethod from '../PaymentMethod';
import ContentOnTable from '../ContentOnTable';
import RenderFormTableByList from '../RenderFieldTableByList';
import { useI18n } from '../../../i18n/useI18n';
import { useSelector } from 'react-redux';
import PERMISSIONS from '../../common/permission';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function ListPurchases() {
    const { t } = useI18n();
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const search = useForm();
    const table = useTable();
    const roles = useSelector((state) => state.businessRole.role);
    const view = useCallback(() => {
        PurchaseService.view()
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => {
                    return <RenderFormTableByList item={item} data={data} />;
                });
                search.setHookRender(resp.message.search);
            })
            .catch((error) => {
                if (error.response?.data?.message) {
                    openPopup({
                        type: 'error',
                        message: error.response.data.message,
                    });
                }
            });
    }, []);

    const handleEdit = (row) => {
        navigate('/purchases?form=edit&id=' + row.id);
    };

    const getPurchases = useCallback(
        (page = 0) => {
            table.setLoading(true);
            PurchaseService.list({
                page: page,
                ...search.formData,
            })
                .then((resp) => {
                    table.setData(resp.message.data);
                    table.setLinks(resp.message.links);
                    table.setLoading(false);
                })
                .catch((error) => {
                    if (error.response?.data?.message) {
                        openPopup({
                            type: 'error',
                            message: error.response.data.message,
                        });
                    }
                });
        },
        [search.formData]
    );

    useEffect(() => {
        getPurchases();
        view();
    }, []);

    return (
        <div>
            <PageHead
                containerClass="mx-4"
                title={t('Purchases')}
                subtitle={t(
                    'Track your offers, approval and payment status'
                )}
            />

            <div className="m-4">
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
                    add={roles?.includes(PERMISSIONS.PURCHASE.CREATE)
                        ? () => navigate('/purchases?form=add') : null}
                    columns={[
                        {
                            label: t('ID'),
                            key: 'id',
                            render: (id) => {
                                return <div style={{
                                    width: 50
                                }}>PU{id}</div>
                            },
                        },
                        {
                            label: t('Supplier'),
                            key: 'supplier_name',
                            render: (value) => {
                                return <div style={{
                                    width: 120
                                }}>
                                    <ContentOnTable value={value} max={15} />
                                </div>
                            },
                        },
                        {
                            label: t('Purchase date'),
                            key: 'purchase_date',
                            render: (date) => {
                                return <div style={{
                                    width: 120
                                }}>{isoToDateTime(date)}</div>
                            },
                        },
                        {
                            label: t('Expected date'),
                            key: 'expected_date',
                            render: (date) => {
                                return <div style={{
                                    width: 120
                                }}>{isoToDateTime(date)}</div>
                            },
                        },
                        {
                            label: t('Shipping fee'),
                            key: 'shipping_fee',
                            render: (value) => (
                                <div style={{
                                    width: 120
                                }}>
                                    <strong><Currencies amount={value} /></strong>
                                </div>
                            ),
                        },
                        {
                            label: t('Payment method'),
                            key: 'payment_method',
                            render: (value) => {
                                return <div style={{
                                    width: 200
                                }}>
                                    <PaymentMethod value={value} />
                                </div>
                            },
                        },
                        { label: t('Buy'), key: 'buy_quantity', render: (value) => {
                            return <div style={{
                                width: 70
                            }}>{value}</div>
                        } },
                        { label: t('Compensation'), key: 'compensation_quantity', render: (value) => {
                            return <div style={{
                                width: 70
                            }}>{value}</div>
                        } },
                        { label: t('Conversion'), key: 'conversion_quantity', render: (value) => {
                            return <div style={{
                                width: 100
                            }}>{value}</div>
                        }  },
                        { label: t('Gift'), key: 'gift_quantity', render: (value) => {
                            return <div style={{
                                width: 70
                            }}>{value}</div>
                        }  },
                        {
                            label: t('Tax'),
                            key: 'tax',
                            render: (value) => (
                                <strong>
                                    <Currencies amount={value} />
                                </strong>
                            ),
                        },
                        {
                            label: t('Status'),
                            key: 'status',
                            render: (value) => <StatusBadge status={value} />,
                        },
                        {
                            label: t('Approved by'),
                            key: 'approved_name',
                            render: (value) => (
                                <span className="badge bg-primary text-uppercase">
                                    {value}
                                </span>
                            ),
                        },
                        {
                            label: t('Created by'),
                            key: 'created_name',
                            render: (value) => (
                                <span className="badge bg-primary text-uppercase">
                                    {value}
                                </span>
                            ),
                        },
                    ]}
                    data={table.data}
                    links={table.links}
                    onEdit={roles?.includes(PERMISSIONS.PURCHASE.SHOW)
                        ? handleEdit : null}
                    callback={getPurchases}
                    loading={table.loading}
                />
            </div>
        </div>
    );
}
