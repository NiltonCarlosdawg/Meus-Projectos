import React, { useRef } from "react";
import Invoice from "./Invoice";
import { useReactToPrint } from "react-to-print";

const Appp = () => {
  const invoiceRef = useRef();

  const handlePrint = useReactToPrint({
    content: () => invoiceRef.current,
  });

  const invoiceData = {
    invoiceNumber: "12345",
    date: "15/01/2025",
    clientName: "João Silva",
    clientAddress: "Rua Exemplo, 123, São Paulo, SP",
    items: [
      { description: "Produto A", quantity: 2, unitPrice: 50 },
      { description: "Produto B", quantity: 1, unitPrice: 100 },
      { description: "Produto C", quantity: 3, unitPrice: 30 },
    ],
  };

  return (
    <div style={{ textAlign: "center", marginTop: "20px" }}>
      <Invoice ref={invoiceRef} invoiceData={invoiceData} />
      <button
        onClick={handlePrint}
        style={{
          marginTop: "20px",
          padding: "10px 20px",
          backgroundColor: "#4CAF50",
          color: "white",
          border: "none",
          cursor: "pointer",
        }}
      >
        Imprimir Fatura
      </button>
    </div>
  );
};

export default Appp;
