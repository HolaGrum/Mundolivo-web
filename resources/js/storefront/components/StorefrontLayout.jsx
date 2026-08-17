import { Outlet } from "react-router-dom";
import Nav from "./Nav";
import Footer from "./Footer";
import SidebarOffCanvas from "./SidebarOffCanvas";
import useOffcanvasStore from "../store/offcanvasStore";

const StorefrontLayout = () => {
  const { isVisible } = useOffcanvasStore();

  return (
    <>
      <Nav />
      <main style={{ paddingTop: "76px" }} className="d-flex flex-column min-vh-100">
        <div className="flex-grow-1">
          <Outlet />
        </div>
      </main>
      {isVisible && <SidebarOffCanvas />}
      <Footer />
    </>
  );
};

export default StorefrontLayout;
