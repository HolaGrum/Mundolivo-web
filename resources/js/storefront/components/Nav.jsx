import { Link } from "react-router-dom";
import logo from "../assets/imgs/logo.svg";
import MyCart from "./MyCart";
import { CiMenuFries } from "react-icons/ci";


const Nav = () => {
  return (
    <nav className="navbar navbar-expand-lg bg-body-tertiary mb-5 custom-navbar fixed-top w-100">
      <div className="container-fluid">
        <Link className="navbar-brand" to="/">
          <img
            src={logo}
            alt="logo"
            style={{ width: "120px" }}
          />
        </Link>
        <button
          className="navbar-toggler border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <CiMenuFries className="menu-icon text-white" />
        </button>
        <div className="collapse navbar-collapse" id="navbarNav">
          <ul className="navbar-nav me-auto">
            <li className="nav-item">
              <Link className="nav-link active" aria-current="page" to="/">
                Inicio
              </Link>
            </li>
            <li className="nav-item">
              <Link className="nav-link" to="/catalog">
                Productos
              </Link>
            </li>
            <li className="nav-item">
              <a className="nav-link" href="#contact">Contacto</a>
            </li>
          </ul>
          <MyCart />
        </div>
      </div>
    </nav>
  );
};

export default Nav;
