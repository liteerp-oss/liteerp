import React from "react";
import { useI18n } from "../../i18n/useI18n";
import EmptyBox from "./Emptybox";

export default function CommonDataTable({
  columns = [],
  data = [],
  onEdit = null,
  onDelete = null,
  filter = null,
  links = [],
  movePage = (page) => {

  },
  loading = false,
  add = null,
  iconEdit = null 
}) {
  const {t} = useI18n();
  return (
    <div className={`card rounded-3 p-4 shadow-sm theme-sidebar-bg theme-title`}>
      <div className="d-flex justify-content-between">
        <div className="col-9">
          {filter}
        </div>
        {add ? <span style={{
          height: 25
        }} onClick={add} className="badge bg-primary text-right btn">{t('Add new')}</span> : null }
        
      </div>
      <div className="table-responsive">
        <table className={`table align-middle mb-0 theme-title theme-table`}>
          <thead>
            <tr className="text-secondary small">
              {columns.map((col, index) => (
                <th key={index} className="fw-normal">
                  {col.label}
                </th>
              ))}
              {(onEdit || onDelete) && <th className="fw-normal"></th>}
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr className="theme-sidebar-bg theme-title">
                <td colSpan={columns.length + 1}>
                  <div
                    className="d-flex justify-content-center align-items-center py-4"
                    style={{ minHeight: "80px" }}
                  >
                    <div className="spinner-border text-primary" role="status">
                      <span className="visually-hidden">Loading...</span>
                    </div>
                  </div>
                </td>
              </tr>

            ) : (
              data.length >= 1 ? data.map((row, idx) => (
                <tr
                  key={idx}
                  className={"theme-sidebar-bg theme-title"}
                >
                  {columns.map((col, index) => (
                    <td key={index}>
                      {col.render
                        ? col.render((col.key.split('.').length >= 2 ? row[col.key.split('.')[0]][col.key.split('.')[1]] : row[col.key]), row)
                        : (col.key.split('.').length >= 2 ? row[col.key.split('.')[0]][col.key.split('.')[1]] : row[col.key])}
                    </td>
                  ))}

                  {(onEdit || onDelete) && (
                    <td style={{
                      width: onEdit && onDelete ? 100 : 50
                    }}>
                      <div className="d-flex gap-2">
                        {onEdit && (
                          <button
                            className="btn btn-sm btn-outline-primary"
                            onClick={() => onEdit(row)}
                          >
                            {iconEdit ??<i className="bi bi-pencil-square"></i> }
                            
                          </button>
                        )}
                        {onDelete && (
                          <button
                            className="btn btn-sm btn-outline-danger"
                            onClick={() => onDelete(row)}
                          >
                            <i className="bi bi-trash"></i>
                          </button>
                        )}
                      </div>
                    </td>
                  )}
                </tr>
              )) : <tr className="theme-sidebar-bg theme-title">
                <td colSpan={columns.length + 1}>
                  <div
                    className="d-flex justify-content-center align-items-center py-4"
                    style={{ minHeight: "80px" }}
                  >
                    <EmptyBox/>
                  </div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
      <div className="mt-3">
        {links.length >= 1 && !loading ? <nav aria-label="Page navigation example">
          <ul className="pagination">
            {links.map((item, index) => {
              return <li key={index}
                onClick={() => {
                  if (item.page === null) {
                    return;
                  }
                  movePage(item.page);
                }}
                className={"page-item " + (item.active ? 'active' : '')}>
                <a className="page-link" dangerouslySetInnerHTML={{ __html: item.label }}></a>
              </li>
            })}
          </ul>
        </nav> : null}

      </div>
    </div>
  );
}
