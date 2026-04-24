import React from 'react'
import LoadImage from './LoadImage'
import StatusBadge from './StatusBadge'
import ContentOnTable from './ContentOnTable'
export default function RenderFieldTableByList({
    item = null,
    data = null
}) {
    return <div>
        {item?.type === 'badge' ?
            <StatusBadge status={data}/>
            : item?.type === 'text' ?
                <span><ContentOnTable value={data}/></span>
                : item?.type === 'image' ?
                    <LoadImage
                        url={data}
                        width={50}
                        height={50}
                    />
                    : item?.type === 'html' ?
                        <div dangerouslySetInnerHTML={{ __html: data }} ></div>
                    : item?.type === 'link' ?
                        <div>
                            <a href={data} target='_blank' >{item.label}</a>
                        </div>
                    : null}
    </div>
}