import React, { useCallback, useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import RenderNav from "./Sidebar/RenderNav";

export default function Sidebar() {
  const nav = useSelector((state) => state.businessRole.nav);
  return (
    <div className="erp-sidebar d-flex flex-column p-3 px-5 mb-5">
      {/* Header */}
      <div className="d-flex align-items-center mb-4">
        <img src="/assets/logo-full.png" height={80} alt='' />
      </div>

      {/* Menu */}
      <ul className="nav nav-pills flex-column mb-auto">
          <RenderNav list={nav ?? []}/>
      </ul>
      <div className="mt-5"></div>
    </div>
  );
}
