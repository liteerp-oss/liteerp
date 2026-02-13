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
      {/* Button */}
      <i
        className="bi bi-list btn theme-title"
        style={{ fontSize: 35, marginTop: 5 }}
        onClick={() => setOpen(true)}
      />

      {/* Overlay */}
      {open && (
        <div
          onClick={() => setOpen(false)}
          style={{
            position: "fixed",
            inset: 0,
            zIndex: 2000,
            background: "rgba(0,0,0,0.4)",
            backdropFilter: "blur(20px)",
            WebkitBackdropFilter: "blur(20px)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            animation: "fadeIn 0.2s ease",
          }}
        >
          {/* Grid */}
          <div
            onClick={(e) => e.stopPropagation()}
            style={{
              width: "90%",
              maxWidth: 900,
              display: "grid",
              gridTemplateColumns: "repeat(auto-fill, minmax(100px, 1fr))",
              gap: 30,
              textAlign: "center",
            }}
          >
            {items.map((item, index) => {
              const content = (
                <div
                  style={{
                    cursor: "pointer",
                    transition: "transform 0.15s ease",
                  }}
                  onMouseEnter={(e) =>
                    (e.currentTarget.style.transform = "scale(1.1)")
                  }
                  onMouseLeave={(e) =>
                    (e.currentTarget.style.transform = "scale(1)")
                  }
                  onClick={() => setOpen(false)}
                >
                  <div
                    style={{
                      width: 80,
                      height: 80,
                      margin: "0 auto",
                      borderRadius: 20,
                      background:
                        "linear-gradient(135deg,#4f46e5,#6366f1)",
                      display: "flex",
                      justifyContent: "center",
                      alignItems: "center",
                      boxShadow: "0 10px 25px rgba(0,0,0,0.3)",
                    }}
                  >
                    <i
                      className={item.icon}
                      style={{ fontSize: 30, color: "#fff" }}
                    />
                  </div>
                  <div
                    style={{
                      marginTop: 10,
                      fontWeight: 500,
                      color: "#fff",
                      fontSize: 14,
                    }}
                  >
                    {item.label}
                  </div>
                </div>
              );

              // Nếu có to → dùng NavLink
              if (item.to) {
                return (
                  <NavLink
                    key={index}
                    to={item.to}
                    style={{ textDecoration: "none" }}
                  >
                    {content}
                  </NavLink>
                );
              }

              // Nếu có link external
              return (
                <a
                  key={index}
                  href={item.link}
                  style={{ textDecoration: "none" }}
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
