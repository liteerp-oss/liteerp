import React from 'react'
import TabsCommon from '../TabsCustom'
import Currencies from '../Currencies'
import TabSummaryContent from './TabSummary/TabSummaryContent'
import { useI18n } from '@/i18n/useI18n'
export function TabSummary({
    data = [],
    title = 'Title'
}){
    const {t} = useI18n();
    const renderContent = (data) => {
        let render = [];
        data.map((item,index) => {
            return render.push(<div key={index}>
                <TabSummaryContent item={item}/>
            </div>)
        })
        return render
    }
    return <div className='p-3 rounded-3 mt-4 card-overview'>
        <h3 className='h5'>{t(title)}</h3>
        <TabsCommon
        checkRole={false}
        navs={[
            {key:"Today" + title,label:t("Today")},
            {key:"Thisweek" + title,label:t("This week")},
            {key:"Thismonth" + title,label:t("This month")},
            {key:"Thisyear" + title,label:t("This year")}
        ]}
        contents={renderContent(data)}
        />
    </div>
} 