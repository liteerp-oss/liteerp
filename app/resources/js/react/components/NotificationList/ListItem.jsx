import React, { use, useRef, useState } from 'react'
import EntityIconType from './EntityIconType'
import EntityMessage from './EntityMessage'
import { usePopup } from '../popups/PopupContext';
import { useDispatch } from 'react-redux';
import { decrementNotificationCount } from '../../redux/NotificationSlice';
import { useI18n } from '@/i18n/useI18n';
import { useNavigate } from 'react-router-dom';
export default function ListItem({
    entity = null,
    update = (item) => { },
    destroy = (item) => { }
}) {
    const navigate = useNavigate();
    const {t} = useI18n();
    const dispatch = useDispatch();
    const { openPopup } = usePopup();
    const [item,setItem] = useState(entity);
    const confirmMarkRead = () => {
        openPopup({
            type: 'warning',
            message: 'confirm_readed',
            onConfirm: () => {
                //item.is_read = true;
                setItem((pre) => {
                    pre.is_read = true;
                    return pre;
                });
                dispatch(decrementNotificationCount())
                update(entity);
            }
        })
    }
    const confirmDelete = () => {
        openPopup({
            type: 'warning',
            message: 'confirm_delete',
            onConfirm: () => {
                setItem(null);
                destroy(entity);
            }
        })
    }
    return item ? <div
        onClick={() => {
            navigate(item.link);
        }}
        className="row align-items-start 
            justify-content-between p-3 mb-3 rounded notification-item theme-sidebar-bg theme-title"
    >
        <div className="col-8 align-items-start d-flex">
            <div
                className="rounded-circle d-flex justify-content-center align-items-center me-3 theme-title"
                style={{
                    width: "36px",
                    height: "36px",
                    flexShrink: 0,
                }}
            >
                <EntityIconType type={item.type} entity_type={item.entity_type} />
            </div>
            <div>
                <div className="theme-title-highlight fw-semibold text-uppercase">
                    {item.title ?? item.entity_type}{" "}#{item.entity_id}
                    {!item.is_read ? (
                        <span
                            className="text-danger ms-1"
                            style={{
                                fontSize: "10px",
                                verticalAlign: "middle",
                            }}
                        >
                            ●
                        </span>
                    ) : null}
                </div>
                <div className="theme-title small">
                    <EntityMessage entity={item} />
                </div>
                <div className="theme-title small mt-1">{item.created_at_human}</div>
            </div>
        </div>
        <div className="text-end col-4">
            {!item.is_read ? (
                <button
                    onClick={confirmMarkRead}
                    className="btn btn-link btn-sm text-decoration-none text-info"
                    style={{ fontSize: "0.85rem" }}
                >
                    {t('mark_as_read')}
                </button>
            ) : null}
            <button
                onClick={confirmDelete}
                className="btn btn-link btn-sm text-decoration-none text-danger"
                style={{ fontSize: "0.85rem" }}
            >
                {t('delete')}
            </button>
        </div>
    </div> : null;

}