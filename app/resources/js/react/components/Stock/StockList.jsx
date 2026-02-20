import React, { useCallback, useEffect, useState } from 'react';
import TabsCustom from '../TabsCustom';
import StockIns from './StockList/IndexStockIns';
import StockOuts from './StockList/IndexStockOuts';
import PageHead from '../PageHead';
import { useI18n } from '@/i18n/useI18n';
export default function StockList() {
    const {t} = useI18n();
    return <div>
        <PageHead
              containerClass='mx-4'
              title='Stocks'
              subtitle='page_stock_subtitle'
              />
        <div className="m-4">
            <TabsCustom
                navs={[{ key: 'stockin', label: t('Stock Ins') },
                { key: 'stockout', label: t('Stock Outs')}]}
                contents={[
                    <StockIns/>,
                    <StockOuts/>
                ]} />
        </div>
    </div>
}
