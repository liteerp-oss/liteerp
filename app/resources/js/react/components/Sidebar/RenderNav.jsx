import React from 'react'
import { NavLink } from 'react-router-dom'
import { useI18n } from '../../../i18n/useI18n'
export default function RenderNav({
    list = []
}) {
  const {t} = useI18n();
    return list.map((item,index) => {
        return <li className="nav-item mb-2" key={index} style={{
          height: 40,
          maxWidth: '100%'
        }}>
          {item.to ? <NavLink to={item.to} className="erp-link" style={{
            display: 'inline-block',
            width: '100%'
          }}>
            <div className='d-flex align-items-center'>
              <div className='col-2'>
                  <div className={item.icon}/>
              </div>
              <div className='col-10'>
                <div className='ml-2'>
                  {t(item.label)}
                </div>
              </div>
            </div>
          </NavLink> : <a href={item.link} className="erp-link" style={{
            display: 'inline-block',
            width: '100%'
          }}>
            <div className='d-flex align-items-center'>
              <div className='col-2'>
                  <div className={item.icon}/>
              </div>
              <div className='col-10'>
                <div className='ml-2'>
                  {t(item.label)}
                </div>
              </div>
            </div>
          </a> }
          
        </li>
    })
}