import React, { useState, useCallback, useEffect } from 'react';
import ListProducts from './AddProduct/ListProduct';
import VerticalCommonTable from '../VerticalCommonTable'
import Currencies from '../Currencies';
import { useSelector } from 'react-redux';
export default function AddProduct() {
    const purchasedetail = useSelector((state) => state.purchasedetail.data);
    return (
        <div className="">

            <div className="">
                    <ListProducts purchase={purchasedetail} />
            </div>
            <div>
                <VerticalCommonTable data={{
                        shipping_fee: purchasedetail?.shipping_fee 
                            ? <Currencies amount={purchasedetail?.shipping_fee}/> : 0,
                        quantity: purchasedetail?.quantity ?? 0,
                        subtotal: purchasedetail?.subtotal 
                            ? <Currencies amount={purchasedetail?.subtotal}/> : 0,
                        total_tax: purchasedetail?.total_tax 
                            ? <Currencies amount={purchasedetail?.total_tax}/> : 0,
                        total: purchasedetail?.total 
                            ? <Currencies amount={purchasedetail?.total}/> : 0,
                    }} />
            </div>
        </div>
    );
}
