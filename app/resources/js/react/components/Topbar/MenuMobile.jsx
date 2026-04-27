import { useI18n } from "@/i18n/useI18n";
import React, { useState, useMemo } from "react";
import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";

export default function MenuMobile() {
  const nav = useSelector((state) => state.businessRole.nav);
  const [open, setOpen] = useState(false);
  const {t} = useI18n();
  return (
    <>
      <i
        className="bi bi-list btn theme-title menu-mobile-button"
        onClick={() => setOpen(true)}
      />
      {open && (
        <div
          className="menu-mobile-overlay"
          onClick={() => setOpen(false)}
        >
          <div
            className="menu-mobile-grid"
            onClick={(e) => e.stopPropagation()}
          >
            {nav.map((item, index) => {
              
                return item.children.map((child, i) => {
                  const content = (
                    <div
                      className="menu-mobile-item"
                      onClick={() => setOpen(false)}
                    >
                      <div className="menu-mobile-icon-wrapper">
                        <i className={`${child.icon} menu-mobile-icon`} />
                      </div>

                      <div className="menu-mobile-label">
                        {t(child.label)}
                      </div>
                    </div>
                  );

                  if (child.to) {
                    return (
                      <NavLink
                        key={i}
                        to={child.to}
                        className="menu-mobile-link"
                      >
                        {content}
                      </NavLink>
                    );
                  }

                  return (
                    <a
                      key={i}
                      href={child.link}
                      className="menu-mobile-link"
                    >
                      {content}
                    </a>
                  );
                })
              
            })}
          </div>
        </div>
      )}
    </>
  );
}
