import { useEffect, useMemo, useState } from "react";
import useCartStore from "./store/cartStore";
import useOffcanvasStore from "./store/offcanvasStore";
import useTotalStore from "./store/totalProductStore";
import useBalanceStore from "./store/balanceStore";
import ProductsList from "./components/ProductsList";
import useFetch from "./hooks/useFetch"; // Importar el custom hook
import TitleTypeWriter from "./components/TitleTypeWriter";
import CategoryFilter from "./components/CategoryFilter";

const App = () => {
  // Llama a `useCartStore` para acceder al estado del carrito y las funciones
  const { cart } = useCartStore();
  const { getTotalProducts } = useTotalStore();
  const { toggleBalanceo } = useBalanceStore();
  const { isVisible, toggleOffcanvas } = useOffcanvasStore();

  // Usar el hook useFetch para obtener los productos
  const productsUrl = import.meta.env.VITE_PRODUCTS_URL || "/api/storefront/products";
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

  // Normalizar datos: `products` puede ser un array o un objeto { products, categories }
  const filteredProducts = useMemo(() => {
    if (!products) return [];
    return Array.isArray(products) ? products : products.products || [];
  }, [products]);

  const totalFiltered = useMemo(() => filteredProducts.length, [filteredProducts]);

  // Categorías disponibles (si vienen en el JSON)
  const categories = useMemo(() => {
    if (!products) return [];
    return Array.isArray(products) ? [] : products.categories || [];
  }, [products]);

  // Estado para categoría seleccionada
  const [selectedCategory, setSelectedCategory] = useState(null);

  // Filtrar por categoría (usa las categorías reales del producto, con respaldo en título/descripción)
  const displayedProducts = useMemo(() => {
    if (!selectedCategory) return filteredProducts;
    const key = selectedCategory.toLowerCase();
    return filteredProducts.filter((p) => {
      const cats = (p.categories || []).map((c) => c.toLowerCase());
      if (cats.includes(key)) return true;
      return (
        (p.title && p.title.toLowerCase().includes(key)) ||
        (p.description && p.description.toLowerCase().includes(key))
      );
    });
  }, [filteredProducts, selectedCategory]);

  return (
    <div className="container mb-5">
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
        ) : categories.length > 0 ? (
          <div className="col-12">
            <div className="row">
              <div className="col-12 col-md-3">
                <CategoryFilter
                  categories={categories}
                  selected={selectedCategory}
                  onSelect={setSelectedCategory}
                />
              </div>
              <div className="col-12 col-md-9">
                {displayedProducts.length > 0 ? (
                  <ProductsList products={displayedProducts} />
                ) : (
                  <p className="text-center">
                    No hay productos en esta categoría.
                  </p>
                )}
              </div>
            </div>
          </div>
        ) : (
          <div className="col-12">
            <p className="text-center">No hay productos disponibles.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default App;
