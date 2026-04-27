import React, { useState } from "react";
import { useI18n } from "../../i18n/useI18n";
import EmptyBox from "./Emptybox";
import { Select } from "./UI/Input/Select";
import { InputForm } from "./UI/Input/InputForm";
import PrimaryButton from "./UI/Buttons/PrimaryButton";
import SecondaryButton from "./UI/Buttons/SecondaryButton";
import { PopupLayout } from "../layouts/PopupLayout";
import RenderFormFieldByList from "./RenderFormFieldByList";
import { useSelector } from "react-redux";
export default function CommonDataTableV2({
    columns = [],
    data = [],
    onEdit = null,
    onShow = null,
    onDelete = null,
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
        default: [{
            key: "order_by",
            placeholder: "Order by",
            options: [
                { value: 'ASC', label: "Oldest" },
                { value: 'DESC', label: "Newest" },
            ],
            type: "select",
            label: "Order by",
            col: "col-6"
        }, {
            key: "keywords",
            placeholder: "Keywords",
            type: "text",
            label: "Search",
            col: "col-6"
        }]
    },
    callback = () => { },
    // table type for custom render table, example: order, customer, invoice,... for use in render function with condition like if(
    type = null,
    // on cases you need custom permission, it is not like format "erp.customer.create" then you can use roles for custom
    roles = {
        edit: null,
        delete: null,
        add: null,
        show: null
    }
}) {
    const { t } = useI18n();
    const [showExtras, setShowExtras] = useState(false)
    const userRoles = useSelector((state) => state.businessRole.role);
    let permission = {
        canEdit: false,
        canDelete: false,
        canAdd: false,
        canShow: false,

    }
    if (type) {
        permission = {
            canEdit: type ? userRoles?.includes('erp.' + type + '.update') : false,
            canDelete: type ? userRoles?.includes('erp.' + type + '.delete') : false,
            canAdd: type ? userRoles?.includes('erp.' + type + '.create') : false,
            canShow: type ? userRoles?.includes('erp.' + type + '.show') : false,
        }
    } else if (roles) {
        permission = {
            canEdit: userRoles?.includes(roles.edit),
            canDelete: userRoles?.includes(roles.delete),
            canAdd: userRoles?.includes(roles.add),
            canShow: userRoles?.includes(roles.show),
        }
    }
    return (
        <div className={`card rounded-3 p-4 shadow-sm theme-sidebar-bg theme-title`}>
            <div className="d-flex justify-content-between">
                <div className="col-9">
                    {config?.default?.length >= 1 && search ? <div className="row">
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
                                                label={t(item?.label)}
                                            />
                                        </div> : null}
                                        {item.type === 'text' ? <div>
                                            <InputForm
                                                name={item.key}
                                                handleChange={search.handleChange}
                                                placeholder={t(item.placeholder)}
                                                value={search.formData?.[item.key]}
                                                required={item?.required}
                                                label={t(item?.label)}
                                            />
                                        </div> : null}
                                    </div>
                                })}
                            </div>
                        </div>
                        <div className="col-4 pt-4">
                            <div className="d-flex">
                                <SecondaryButton
                                    width={50}
                                    label={
                                        <div>
                                            <i className="bi bi-gear-wide-connected"></i>
                                        </div>
                                    }
                                    onClick={() => setShowExtras(true)}
                                />
                                <div style={{
                                    marginLeft: 10
                                }}>
                                    <PrimaryButton loading={loading} width={100} onClick={() => callback()} label={t("Search")} />
                                </div>
                            </div>
                        </div>
                    </div> : null}
                </div>
                {add && permission.canAdd ? <span style={{
                    height: 25
                }} onClick={add} className="badge bg-primary text-right btn">{t('Add new')}</span> : null}

            </div>
            <div className="table-responsive">
                <table className={`table align-middle mb-0 
                        theme-title theme-table 
                        table-hover`}
                    style={{ width: "max-content", minWidth: "100%" }}
                >
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

                                    {(onEdit || onDelete || onShow) && (
                                        <td style={{
                                            width: 150
                                        }}>
                                            <div className="d-flex gap-2">
                                                {onEdit && permission.canEdit ? (
                                                    <button
                                                        style={{
                                                            width: 50
                                                        }}
                                                        className="btn btn-sm btn-outline-primary"
                                                        onClick={() => onEdit(row)}
                                                    >
                                                        {iconEdit ?? <i className="bi bi-pencil-square"></i>}

                                                    </button>
                                                ) : null}
                                                {onDelete && permission.canDelete ? (
                                                    <button
                                                        style={{
                                                            width: 50
                                                        }}
                                                        className="btn btn-sm btn-outline-danger"
                                                        onClick={() => onDelete(row)}
                                                    >
                                                        <i className="bi bi-trash"></i>
                                                    </button>
                                                ) : null}
                                                {onShow && permission.canShow ? (
                                                    <button
                                                        style={{
                                                            width: 50
                                                        }}
                                                        className="btn btn-sm btn-outline-secondary"
                                                        onClick={() => onShow(row)}
                                                    >
                                                        <i className="bi bi-eye"></i>
                                                    </button>
                                                ) : null}
                                            </div>
                                        </td>
                                    )}
                                </tr>
                            )) : <tr className="theme-sidebar-bg theme-title"
                                onClick={add && permission.canAdd ? add : null }
                                >
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
            {showExtras ? <PopupLayout
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
                    {search.hookRender?.length === 0 ? <EmptyBox message={t("No extension to render")} /> : null}
                </div>
            </PopupLayout> : null}

        </div>
    );
}
