import React from "react";

const Invoice = React.forwardRef((props, ref) => {
  const { invoiceData } = props;

  return (
    <div ref={ref} style={{ padding: "20px", fontFamily: "Arial, sans-serif" }}>
      <h1>Fatura</h1>
      <p><strong>Fatura Nº:</strong> {invoiceData.invoiceNumber}</p>
      <p><strong>Data:</strong> {invoiceData.date}</p>

      <h2>Detalhes do Cliente</h2>
      <p><strong>Nome:</strong> {invoiceData.clientName}</p>
      <p><strong>Endereço:</strong> {invoiceData.clientAddress}</p>

      <h2>Itens</h2>
      <table style={{ width: "100%", borderCollapse: "collapse" }}>
        <thead>
          <tr>
            <th style={{ border: "1px solid black", padding: "5px" }}>Descrição</th>
            <th style={{ border: "1px solid black", padding: "5px" }}>Quantidade</th>
            <th style={{ border: "1px solid black", padding: "5px" }}>Preço Unitário</th>
            <th style={{ border: "1px solid black", padding: "5px" }}>Total</th>
          </tr>
        </thead>
        <tbody>
          {invoiceData.items.map((item, index) => (
            <tr key={index}>
              <td style={{ border: "1px solid black", padding: "5px" }}>{item.description}</td>
              <td style={{ border: "1px solid black", padding: "5px" }}>{item.quantity}</td>
              <td style={{ border: "1px solid black", padding: "5px" }}>R${item.unitPrice.toFixed(2)}</td>
              <td style={{ border: "1px solid black", padding: "5px" }}>R${(item.quantity * item.unitPrice).toFixed(2)}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <h2 style={{ textAlign: "right" }}>
        <strong>Total:</strong> R${invoiceData.items.reduce((sum, item) => sum + item.quantity * item.unitPrice, 0).toFixed(2)}
      </h2>
    </div>
  );
});

export default Invoice;
