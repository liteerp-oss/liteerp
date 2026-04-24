import React, { useCallback, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import OrderService from '../../services/OrderService';
import { usePopup } from '../popups/PopupContext';
import { useForm } from '../../libraries/handleInput';
import useTable from '../../libraries/handleTable';
import PageHead from '../PageHead';
import StatusBadge from '../StatusBadge';
import ContentOnTable from '../ContentOnTable';
import PaymentMethod from '../PaymentMethod';
import RenderFieldTableByList from '../RenderFieldTableByList';
import { useI18n } from '../../../i18n/useI18n';
import CommonDataTableV2 from '../CommonDataTableV2';

export default function ListOrder() {
    const { t,lang } = useI18n();
    const navigate = useNavigate();
    const { openPopup } = usePopup();
    const table = useTable();
    const search = useForm();
    const handleShow = (row) => {
        navigate('/orders?form=edit&id=' + row.id);
    };

    const getOrders = useCallback(
        (page = 0) => {
            table.setLoading(true);
            OrderService.list({
                page,
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

    const view = useCallback(() => {
        OrderService.view()
            .then((resp) => {
                table.addColums(resp.message.index, (item, data) => {
                    return (
                        <RenderFieldTableByList item={item} data={data} />
                    );
                });
                search.setHookRender(resp.message.search);
            })
            .catch(() => { });
    }, []);

    useEffect(() => {
        table.setColums([
            { label: t('ID'), key: 'id' },
            {
                label: t('Customer name'),
                key: 'customer_name',
                render: (value) => <ContentOnTable value={value} />,
            },
            {
                label: t('Address shipping'),
                key: 'customer_address',
                render: (value) => <ContentOnTable value={value} />,
            },
            {
                label: t('Order type'),
                key: 'type',
                render: (value) => (
                    <StatusBadge status={value} />
                ),
            },
            { label: t('Order no'), key: 'order_no' },
            {
                label: t('Products'),
                key: 'total_product',
                render: (value) => <span>{value}</span>,
            },
            {
                label: t('Status'),
                key: 'status',
                render: (value) => <StatusBadge status={value} />,
            },
            {
                label: t('Payment'),
                key: 'payment_method',
                render: (value) => <PaymentMethod value={value} />,
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
            {
                label: t('Approved by'),
                key: 'approved_name',
                render: (value) => (
                    <span className="badge bg-primary text-uppercase">
                        {value}
                    </span>
                ),
            },
        ]);
        getOrders();
        view();
    }, [lang]);

    return (
        <div>
            <PageHead
                containerClass="mx-4"
                title={t('Orders')}
                subtitle={t('Manage orders')}
            />

            <div className="m-4">
                <CommonDataTableV2
                    search={search}
                    add={() => navigate('/orders?form=add')}
                    columns={table.colums}
                    data={table?.data}
                    links={table?.links}
                    onShow={handleShow}
                    loading={table.loading}
                    callback={getOrders}
                    type={'order'}
                />
            </div>
        </div>
    );
}
