import React, { useState } from "react";
import { useI18n } from "../../i18n/useI18n";
import EmptyBox from "./Emptybox";
import { Select } from "./UI/Input/Select";
import { InputForm } from "./UI/Input/InputForm";
import PrimaryButton from "./UI/Buttons/PrimaryButton";
import SecondaryButton from "./UI/Buttons/SecondaryButton";
import { PopupLayout } from "../layouts/PopupLayout";
import RenderFormFieldByList from "./RenderFormFieldByList";

export default function CommonDataTableV2({
    columns = [],
    data = [],
    onEdit = null,
    onDelete = null,
    filter = null,
    links = [],
    loading = false,
    add = null,
    iconEdit = null,
    search = {
        formData: null,
        formErrors: null,
        handleChange: null,
        handleChangeByKey: null,
        hookRender: [],
        setFormData: null 
    },
    config = {
        default: [],
        extras: []
    },
    callback = () => {}
}) {
    const { t } = useI18n();
    const [showExtras,setShowExtras] = useState(false)
    return (
        <div className={`card rounded-3 p-4 shadow-sm theme-sidebar-bg theme-title`}>
            <div className="d-flex justify-content-between">
                <div className="col-9">
                    <div className="row">
                        <div className="col-8">
                            <div className="row">
                                {config?.default?.map((item, index) => {
                                    return <div className={item?.col ?? "col-4"} key={index}>
                                        {item.type === 'select' ? <div>
                                            <Select
                                                name={item?.key}
                                                handleChange={search.handleChange}
                                                options={item?.options}
                                                placeholder={item?.placeholder}
                                                value={search.formData?.[item.key]}
                                                required={item?.required}
                                                label={item?.label}
                                            />
                                        </div> : null}
                                        {item.type === 'text' ? <div>
                                            <InputForm
                                                name={item.key}
                                                handleChange={search.handleChange}
                                                placeholder={item.placeholder}
                                                value={search.formData?.[item.key]}
                                                required={item?.required}
                                                label={item?.label}
                                            />
                                        </div> : null}
                                    </div>
                                })}
                            </div>
                        </div>
                        <div className="col-1 pt-4">
                            <SecondaryButton
                                width={70}
                                label={
                                    <div>
                                        <i className="bi bi-gear-wide-connected"></i>
                                    </div>
                                } 
                                onClick={() => setShowExtras(true)}
                                />
                        </div>
                        <div className="col-2 pt-4">
                            <PrimaryButton onClick={() => callback()} label={t("Search")} />
                        </div>
                    </div>
                    {filter}
                </div>
                {add ? <span style={{
                    height: 25
                }} onClick={add} className="badge bg-primary text-right btn">{t('Add new')}</span> : null}

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
                                                        {iconEdit ?? <i className="bi bi-pencil-square"></i>}

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
                                        <EmptyBox />
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
                                    callback(item.page);
                                }}
                                className={"page-item " + (item.active ? 'active' : '')}>
                                <a className="page-link" dangerouslySetInnerHTML={{ __html: item.label }}></a>
                            </li>
                        })}
                    </ul>
                </nav> : null}

            </div>
            {showExtras ?<PopupLayout
                onClose={() => {
                    search.setFormData(null);
                    setShowExtras(false);
                }}
                title={t("Extras filter")}
                onConfirm={() => {
                    setShowExtras(false)
                }}
                cancelText={t("Close")} 
                confirmText={t("Confirm")}>
                <div>
                    {search.hookRender.map((item, index) => {
                        return <div className="form-group mt-3" key={index}>
                            <RenderFormFieldByList item={item} form={search} />
                        </div>
                    })}
                    {search.hookRender?.length === 0 ? <EmptyBox/> : null }
                </div>
            </PopupLayout> : null }
            
        </div>
    );
}
