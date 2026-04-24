import React, { useCallback, useEffect, useRef, useState } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import NotificationService from "../services/NotificationService";
import useTable from '../libraries/handleTable'
import { usePopup } from '../components/popups/PopupContext'
import LoadingBox from './LoadingBox'
import ListItem from "./NotificationList/ListItem";
import { useForm } from '../libraries/handleInput';
import { Select } from '../components/UI/Input/Select'
import EmptyBox from "./Emptybox";
import { useI18n } from "@/i18n/useI18n";
const NotificationList = () => {
  const {t} = useI18n();
  const [currentPage, setCurrentPage] = useState(0);
  const [lastPage, setLastPage] = useState(0);
  const [types, setTypes] = useState([]);
  const table = useTable();
  const search = useForm();
  const { openPopup } = usePopup();
  const getListType = useCallback(() => {
    NotificationService.listType()
      .then((resp) => {
        setTypes(resp.message)
      })
      .catch((error) => {

      })
  }, []);
  const getList = useCallback((page = 0) => {
    table.setLoading(true)
    NotificationService.list({
      page: page,
      type: search.formData?.type ?? ''
    })
      .then((resp) => {
        if (page === 0) {
          table.setData(resp.message.data)
        } else {
          table.setData((data) => {
            return data.concat(resp.message.data);
          });
        }

        table.setTotal(resp.message.total);
        table.setLoading(false);
        setLastPage(resp.message.last_page);
      })
      .catch((error) => {
        if (error.response?.data?.message) {
          openPopup({
            type: 'error',
            message: error.response?.data?.message
          })
        }
        table.setLoading(false)
      })
  }, [search.formData?.type]);
  const update = useCallback((row) => {
    NotificationService.update(row)
      .then((resp) => {
      })
      .catch((error) => {
        if (error.response?.data?.message) {
          openPopup({
            type: 'error',
            message: error.response?.data?.message
          })
        }
      })
  }, [])
  const destroy = useCallback((row) => {
    NotificationService.delete(row)
      .then((resp) => {
      })
      .catch((error) => {
        if (error.response?.data?.message) {
          openPopup({
            type: 'error',
            message: error.response?.data?.message
          })
        }
      })
  }, [])

  useEffect(() => {

    getList(currentPage);
  }, [currentPage, search.formData?.type]);

  useEffect(() => {
    getListType();
  }, []);

  return (
    <div
      className="container-fluid py-4"
      style={{ minHeight: "100vh" }}
    >
      <div className="mx-auto col-xs-12 col-sm-12 col-md-10 col-lg-6">
        <div className="d-flex justify-content-between align-items-center mb-3">
          <h5 className="theme-title-highlight mb-0">{t('all_notifications')}</h5>
          <div className="d-flex align-items-center">
            <span className="badge bg-secondary me-2">
              {table.total} {t('notifications')}
            </span>
            <Select className="form-select form-select-sm border-secondary"
              name="type"
              handleChange={(e) => {
                table.setData([]);
                setCurrentPage(0);
                search.handleChange(e);
              }}
              value={search.formData?.type}
              errorMessage={search.formErrors?.type}
              options={types.map((item, index) => {
                return {
                  value: item.entity_type,
                  label: item.entity_type
                }
              })}
            />
          </div>
        </div>
        {table.data.map((n, key) => (
          <div key={key}>
            <ListItem update={update} destroy={destroy} entity={n} />
          </div>
        ))}
        {table.loading ? <LoadingBox /> : <div className="text-center h2" onClick={() => {
          setCurrentPage((current) => {
            if (current === lastPage) {
              return current;
            }
            return current + 1;
          })
        }}>
          {table.data?.length === 0 
          ? <div>
            <EmptyBox/>
          </div>
          : currentPage === lastPage 
          ? null 
          : <i className="bi bi-arrow-bar-down btn"></i>}
        </div>}


      </div>
    </div>
  );
};

export default NotificationList;
