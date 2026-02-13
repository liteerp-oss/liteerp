import React, { useState, useMemo } from "react";
import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";

export default function MenuMobile() {
  const nav = useSelector((state) => state.businessRole.nav);
  const [open, setOpen] = useState(false);

  const items = useMemo(() => {
    if (!nav) return [];
    return nav.filter((item) => item.to || item.link);
  }, [nav]);

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
            {items.map((item, index) => {
              const content = (
                <div
                  className="menu-mobile-item"
                  onClick={() => setOpen(false)}
                >
                  <div className="menu-mobile-icon-wrapper">
                    <i className={`${item.icon} menu-mobile-icon`} />
                  </div>

                  <div className="menu-mobile-label">
                    {item.label}
                  </div>
                </div>
              );

              if (item.to) {
                return (
                  <NavLink
                    key={index}
                    to={item.to}
                    className="menu-mobile-link"
                  >
                    {content}
                  </NavLink>
                );
              }

              return (
                <a
                  key={index}
                  href={item.link}
                  className="menu-mobile-link"
                >
                  {content}
                </a>
              );
            })}
          </div>
        </div>
      )}
    </>
  );
}
