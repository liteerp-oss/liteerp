import React from 'react'
import { NavLink } from 'react-router-dom'
import { useI18n } from '../../../i18n/useI18n'

export default function RenderNav({ list = [] }) {
  const { t } = useI18n();

  return (
    <div className="nav flex-column">

      {list.map((group, index) => group.children.length >= 1 ? <div key={index} className="mb-4">

          <div className="text-uppercase small mb-2 px-3 theme-title fw-bold">
            {t(group.label)}
          </div>

          {/* ===== Children ===== */}
          <div className="nav flex-column">

            {group.children.map((item, i) => (
              <NavLink
                key={i}
                to={item.to}
                className={({ isActive }) =>
                  `nav-link d-flex align-items-center px-3 py-2 rounded-3 ${isActive ? 'active bg-primary text-white' : 'theme-title'
                  }`
                }
              >
                <i className={`${item.icon} me-2`} />
                <span>{t(item.label)}</span>
              </NavLink>
            ))}

          </div>
        </div> : null

      )}

    </div>
  )
}
