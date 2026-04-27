import React,{ lazy, Suspense } from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Wrapper from "@/react/wrappers/Wrapper";
import routeRegistry from "@core/RouteRegistry";

// Lazy pages
const Login = lazy(() => import("./react/pages/login"));
const Dashboard = lazy(() => import("./react/pages/dashboard"));
const Product = lazy(() => import("./react/pages/product"));
const Order = lazy(() => import("./react/pages/order"));
const Customer = lazy(() => import("./react/pages/customer"));
const Warehouse = lazy(() => import("./react/pages/warehouse"));
const InvoiceIns = lazy(() => import("./react/pages/invoiceins"));
const Setting = lazy(() => import("./react/pages/setting"));
const User = lazy(() => import("./react/pages/user"));
const Register = lazy(() => import("./react/pages/register"));
const Business = lazy(() => import("./react/pages/business"));
const VerifyAccount = lazy(() => import("./react/pages/verify-account"));
const Notification = lazy(() => import("./react/pages/notification"));
const StockIns = lazy(() => import("./react/pages/stockin"));
const Purchases = lazy(() => import("./react/pages/purchases"));
const Suppliers = lazy(() => import("./react/pages/suppliers"));
const Shipping = lazy(() => import("./react/pages/shipping"));
const Inventory = lazy(() => import("./react/pages/Inventory"));
const ActivityLogs = lazy(() => import("./react/pages/activity-logs"));
const Profile = lazy(() => import("./react/pages/Profile"));
const ForgetPassword = lazy(() => import("./react/pages/forget-password"));
const ResetPassword = lazy(() => import("./react/pages/reset-password"));
const Logout = lazy(() => import("./react/pages/Logout"));
const Extensions = lazy(() => import("./react/pages/extensions"));
const Permissions = lazy(() => import("./react/pages/permissions"));
const InvoiceOuts = lazy(() => import("./react/pages/Invoiceouts"));
const CustomInvoiceIns = lazy(() => import("./react/pages/custominvoiceins"));
const CustomInvoiceOuts = lazy(() => import("./react/pages/custominvoiceouts"));
const CustomerGroup = lazy(() => import("./react/pages/customergroup"));
const CategoryProduct = lazy(() => import("./react/pages/categoryproduct"));
const Pricelist = lazy(() => import("./react/pages/pricelist"));
const StockOuts = lazy(() => import("./react/pages/stockout"));
const InventoryAdjustments = lazy(() => import("./react/pages/inventoryadjustments"));

const App = () => {
  const routes = routeRegistry.all();
  return (
    <Wrapper>
      <BrowserRouter basename="/dashboard">
        <Routes>
          <Route path="/" element={<Dashboard />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/verify-account" element={<VerifyAccount />} />
          <Route path="/products" element={<Product />} />
          <Route path="/category-product" element={<CategoryProduct />} />
          <Route path="/price-list" element={<Pricelist />} />
          <Route path="/orders" element={<Order />} />
          <Route path="/customers" element={<Customer />} />
          <Route path="/customer-groups" element={<CustomerGroup />} />
          <Route path="/warehouses" element={<Warehouse />} />
          <Route path="/invoice-ins" element={<InvoiceIns />} />
          <Route path="/invoice-outs" element={<InvoiceOuts/>}/>
          <Route path="/custom-invoice-ins" element={<CustomInvoiceIns />} />
          <Route path="/custom-invoice-outs" element={<CustomInvoiceOuts/>}/>
          <Route path="/settings" element={<Setting />} />
          <Route path="/users" element={<User />} />
          <Route path="/business" element={<Business />} />
          <Route path="/notification" element={<Notification />} />
          <Route path="/stock-ins" element={<StockIns />} />
          <Route path="/stock-outs" element={<StockOuts />} />
          <Route path="/inventories" element={<Inventory />} />
          <Route path="/inventories-adjustments" element={<InventoryAdjustments />} />
          <Route path="/purchases" element={<Purchases />} />
          <Route path="/suppliers" element={<Suppliers />} />
          <Route path="/shippings" element={<Shipping />} />
          <Route path="/activity-logs" element={<ActivityLogs />} />
          <Route path="/profile" element={<Profile />} />
          <Route path="/forget-password" element={<ForgetPassword />} />
          <Route path="/reset-password" element={<ResetPassword />} />
          <Route path="/logout" element={<Logout />} />
          <Route path="/extensions" element={<Extensions />} />
          <Route path="/permission-group" element={<Permissions />} />
          {routes.map(r => (
            <Route key={r.path} path={r.path} element={<r.component />} />
          ))}
        </Routes>
      </BrowserRouter>
    </Wrapper>
  );
};

if (document.getElementById("app")) {
  ReactDOM.createRoot(document.getElementById("app")).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}
