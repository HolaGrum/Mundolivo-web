import { useEffect, useMemo, useState } from "react";
import useCartStore from "./store/cartStore";
import useOffcanvasStore from "./store/offcanvasStore";
import useTotalStore from "./store/totalProductStore";
import useBalanceStore from "./store/balanceStore";
import ProductsList from "./components/ProductsList";
import Footer from "./components/Footer";
import Nav from "./components/Nav";
import SidebarOffCanvas from "./components/SidebarOffCanvas";
import useFetch from "./hooks/useFetch"; // Importar el custom hook
import TitleTypeWriter from "./components/TitleTypeWriter";

const App = () => {
  // Llama a `useCartStore` para acceder al estado del carrito y las funciones
  const { cart } = useCartStore();
  const { getTotalProducts } = useTotalStore();
  const { toggleBalanceo } = useBalanceStore();
  const { isVisible, toggleOffcanvas } = useOffcanvasStore();

  // Usar el hook useFetch para obtener los productos
  const productsUrl = import.meta.env.VITE_PRODUCTS_URL || "/json/products.json";
  const { data: products, loading, error } = useFetch(productsUrl);

  // Estado para simular carga mínima
  const [isSimulatedLoading, setIsSimulatedLoading] = useState(true);

  // UseEffect para manejar el estado de balanceo y la carga mínima
  useEffect(() => {
    const timer = setTimeout(() => {
      setIsSimulatedLoading(false); // Desactiva la carga simulada después de 3 segundos
    }, 1000);

    if (cart.length > 0) {
      const totalProductsBalanceo = getTotalProducts(cart); // Calcula los productos únicos
      // Abre el carrito solo si no está visible
      if (!isVisible) {
        toggleOffcanvas(true);
      }

      // Activa la animación si hay productos únicos
      if (totalProductsBalanceo > 0) {
        toggleBalanceo(true);
      }

      return () => clearTimeout(timer); // Limpiar el temporizador al desmontar el componente
    }

    return () => clearTimeout(timer); // Limpiar el temporizador al desmontar el componente
  }, [cart, getTotalProducts, toggleBalanceo, toggleOffcanvas]);

  // Sin filtro: mostrar todos los productos
  const filteredProducts = useMemo(() => products || [], [products]);
  const totalFiltered = useMemo(() => products?.length || 0, [products]);

  return (
    <>
      <Nav />

      <div className="container mt-5 mb-5">
        <TitleTypeWriter />

        <div className="row">
          {loading || isSimulatedLoading ? (
            <div className="col-12 text-center my-5">
              <div className="spinner-border text-warning" role="status">
                <span className="visually-hidden">Cargando...</span>
              </div>
            </div>
          ) : error ? (
            <div className="col-12">
              <h2 className="text-center text-danger">
                Error cargando productos: {error.message}
              </h2>
            </div>
          ) : filteredProducts.length > 0 ? (
            <div className="col-12">
              <ProductsList products={filteredProducts} />
            </div>
          ) : (
            <div className="col-12">
              <p className="text-center">No hay productos disponibles.</p>
            </div>
          )}
        </div>
      </div>

      {/* Mostrar el SidebarOffCanvas, carrito de compras */}
      {isVisible && <SidebarOffCanvas />}

      <Footer />
    </>
  );
};

export default App;
